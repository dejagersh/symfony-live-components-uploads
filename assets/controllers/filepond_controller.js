import { Controller } from "@hotwired/stimulus"
import * as FilePond from 'filepond';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    static values = {
        property: String
    }

    async connect() {
        /**
         * Somehow get a hold of the live component this filepond element is nested in...
         */
        this.liveComponent = await getComponent(this.element.parentElement.parentElement);

        // Remove `h-16 rounded-lg bg-gray-100`
        this.element.classList.remove('h-[79px]', 'rounded-lg', 'bg-[#f1f0ef]');

        let querySelector = this.element.querySelector('input[type="file"]');
        querySelector.classList.remove('invisible')
        FilePond.create(
            querySelector,
            {
                server: {
                    process: async (fieldName, file, metadata, load, error, progress) => {
                        this.liveComponent.files(this.propertyValue, { files: [file] });

                        await this.liveComponent.action('_uploadFile', {
                            propertyName: this.propertyValue,
                        });

                        /**
                         * Prevent this file from being sent again on the next upload
                         */
                        this.liveComponent.files(this.propertyValue, {})

                        load(file);
                    },
                    revert: (filename, load, error) => {
                        this.liveComponent.action('_deleteFile', {
                            propertyName: this.propertyValue,
                        })
                        load();
                    }
                }
            }
        );
    }
}