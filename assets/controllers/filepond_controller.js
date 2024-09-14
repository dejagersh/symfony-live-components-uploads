import { Controller } from "@hotwired/stimulus"
import * as FilePond from 'filepond';
import { getComponent } from '@symfony/ux-live-component';

export default class extends Controller {
    static values = {
        property: String
    }

    async connect() {
        this.liveComponent = await getComponent(this.element.parentElement.parentElement);

        FilePond.create(
            this.element.querySelector('input[type="file"]'),
            {
                server: {
                    process: async (fieldName, file, metadata, load, error, progress) => {
                        this.liveComponent.files(this.propertyValue, { files: [file] });

                        await this.liveComponent.action('_uploadFile', {
                            property: this.propertyValue,
                        });

                        this.liveComponent.files(this.propertyValue, {})

                        load(file);
                    },
                    revert: (filename, load, error) => {
                        this.liveComponent.action('deleteFile')
                        load();
                    }
                }
            }
        );
    }
}