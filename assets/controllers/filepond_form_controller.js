import {Controller} from "@hotwired/stimulus";
import * as FilePond from 'filepond';
import 'filepond/dist/filepond.css';
import {getComponent} from '@symfony/ux-live-component';

export default class extends Controller {
    static values = {
        fieldName: String,
        live: Boolean,
    }

    async connect() {
        const fileInputElement = this.element.querySelector('input[type="file"]');

        if (!this.liveValue) {
            FilePond.create(fileInputElement, {
                storeAsFile: true,
            })
        } else {
            const liveComponent = await this.findLiveComponent();

            FilePond.create(fileInputElement, {
                server: {
                    process: async (fieldName, file, metadata, load, error, progress) => {
                        liveComponent.files('file', {files: [file]});

                        await liveComponent.action('_uploadFile', {
                            fieldName: this.fieldNameValue,
                        });

                        /**
                         * Prevent this file from being sent again on the next upload
                         */
                        liveComponent.files('file', {})

                        load(file);
                    },
                    revert: (filename, load, error) => {
                        liveComponent.action('_deleteFile', {
                            propertyName: this.propertyValue,
                        })
                        load();
                    }
                }
            });
        }
    }

    /**
     * Find the live component this element is nested in
     *
     * @returns {Promise<unknown>}
     */
    async findLiveComponent() {
        let currentElement = this.element.parentElement;

        while (true) {
            try {
                return await getComponent(currentElement);
            } catch (e) {
                // Do nothing
            }

            if (!currentElement.parentElement) {
                throw new Error('Live component not found');
            }
            currentElement = currentElement.parentElement;
        }
    }
}