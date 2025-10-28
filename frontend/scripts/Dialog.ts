import Swal from 'sweetalert2';
import { toast } from 'vue-sonner';
import gsap from 'gsap';

export default class Dialog {
    static #instance: Dialog;

    static ICON_INFO = 'info';
    static ICON_SUCCESS = 'success';
    static ICON_WARNING = 'warning';
    static ICON_ERROR = 'error';
    static ICON_QUESTION = 'question';
    static #LOADER_ID = '#main-loader';

    static SUCCESS_BUTTON_ICON =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-dasharray="24" stroke-dashoffset="24" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11L11 17L21 7"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.4s" values="24;0"/></path></svg>';

    static CANCEL_BUTTON_ICON =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.758 17.243L12.001 12m5.243-5.243L12 12m0 0L6.758 6.757M12.001 12l5.243 5.243"/></svg>';

    static CONFIRM_BUTTON = {
        icon: '',
        text: () => `
        <span class="swal-btn-content flex items-center gap-2 justify-center">
            ${Dialog.SUCCESS_BUTTON_ICON}
            <span class="capitalize button-label">Confirmar</span>
        </span>
    `,
        classes: 'bg-color-scale text-white rounded-lg hover:bg-color-scale-hover'
    };

    static CANCEL_BUTTON = {
        icon: '',
        text: () => `  <span class="swal-btn-content flex items-center gap-2 justify-center">
            ${Dialog.CANCEL_BUTTON_ICON}
            <span class="capitalize button-label">Cancelar</span>
        </span>`,
        classes: ' bg-white-scale text-black rounded-lg hover:bg-gray-200'
    };

    public static getInstance(): Dialog {
        if (!this.#instance) this.#instance = new Dialog();
        return this.#instance;
    }

    showLoader(duration: number = 1, delay: number = 0) {
    gsap.to(Dialog.#LOADER_ID, { duration, delay, autoAlpha: 1 });
  }

  hideLoader(duration: number = 1, delay: number = 0) {
    gsap.to(Dialog.#LOADER_ID, { duration, delay, autoAlpha: 0 });
  }

    #buttonDefaults() {
        return {
            confirmButtonText: Dialog.CONFIRM_BUTTON.text(),
            cancelButtonText: Dialog.CANCEL_BUTTON.text(),
            customClass: { confirmButton: Dialog.CONFIRM_BUTTON.classes, cancelButton: Dialog.CANCEL_BUTTON.classes },
            reverseButtons: true,
            willOpen: () => {
                Swal.getConfirmButton()?.classList.remove('swal2-confirm');
                Swal.getCancelButton()?.classList.remove('swal2-cancel');
            }
        };
    }

    #toastDefaults() {
        return {
            closeButton: true,
            style: {
                backgroundColor: 'var(--toast-bg)',
                color: 'var(--toast-text)',
                border: '1px solid var(--toast-border)',
                borderRadius: 'var(--toast-radius)'
            }
        };
    }

    async #fire(options: any = {}): Promise<any> {
        return await Swal.fire({ ...this.#buttonDefaults(), ...options });
    }
    async message(options: any = {}): Promise<any> {
        return this.#fire({ title: '', icon: Dialog.ICON_INFO, ...options });
    }
    async success(options: any = {}): Promise<any> {
        return this.#fire({ title: '', icon: Dialog.ICON_SUCCESS, ...options });
    }
    async info(options: any = {}): Promise<any> {
        return this.#fire({ title: '', icon: Dialog.ICON_INFO, ...options });
    }
    async warning(options: any = {}): Promise<any> {
        return this.#fire({ title: '', icon: Dialog.ICON_WARNING, ...options });
    }
    async error(options: any = {}): Promise<any> {
        return this.#fire({ title: '', icon: Dialog.ICON_ERROR, ...options });
    }
    async question(options: any = {}): Promise<any> {
        return this.#fire({
            title: 'Confirmação',
            html: 'Tem certeza que deseja continuar?',
            icon: Dialog.ICON_QUESTION,
            showCancelButton: true,
            ...this.#buttonDefaults(),
            ...options
        });
    }

    async errorConfirm(options: any = {}): Promise<any> {
        return this.#fire({
            title: 'Atenção',
            html: 'Tem certeza que deseja continuar?',
            icon: Dialog.ICON_ERROR,
            showCancelButton: false,
            reverseButtons: true,
            ...options,
            confirmButtonText: 'Entendi',
            willOpen: () => {
                Swal.getConfirmButton()?.classList.remove('swal2-confirm');
            }
        });
    }

    async warningConfirm(options: any = {}): Promise<any> {
        return this.#fire({
            title: 'Atenção',
            html: 'Tem certeza que deseja continuar?',
            icon: Dialog.ICON_WARNING,
            reverseButtons: true,
            ...options,
            showCancelButton: true,
            confirmButtonText: 'Continuar',
            willOpen: () => {
                Swal.getConfirmButton()?.classList.remove('swal2-confirm');
                Swal.getCancelButton()?.classList.remove('swal2-cancel');
            }
        });
    }

    async toastSuccess(text: string, options: any = {}) {
        return toast.success(text, {
            ...this.#toastDefaults(),
            ...options,
            classes: { icon: 'text-color-scale' }
        });
    }

    async toastInfo(text: string, options: any = {}) {
        return toast.info(text, {
            ...this.#toastDefaults(),
            ...options,
            classes: { icon: 'text-blue-500' }
        });
    }

    async toastWarn(text: string, options: any = {}) {
        return toast.warning(text, {
            ...this.#toastDefaults(),
            ...options,
            classes: { icon: 'text-yellow-500' }
        });
    }

    async toastError(text: string, options: any = {}) {
        return toast.error(text, {
            ...this.#toastDefaults(),
            ...options,
            classes: { icon: 'text-red-500' }
        });
    }

    async toast(text: string, options: any = {}) {
        return toast(text, {
            ...this.#toastDefaults(),
            ...options
        });
    }
}