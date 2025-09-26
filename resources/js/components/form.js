export default (name = 'form', data = {}) => ({
    loading: false,
    token: document.head.querySelector('meta[name="csrf-token"]').content,
    success: false,
    errors: {},

    init() {
        this.submitHandler = this.submit.bind(this);
        this.$el.addEventListener('submit', this.submitHandler);

        this.$el.dataset.method = this.$el.dataset.method || 'post';
        this.$el.dataset.url = this.$el.dataset.url;
    },

    async submit(event) {
        event?.preventDefault();
        this.toggle();
        this.resetErrors();

        const formData = new FormData(this.$el);
        const formMethod = this.$el.dataset.method.toLowerCase();
        const formUrl = this.$el.dataset.url;

        this.prepareFormData(formData, formMethod, data);

        try {
            const response = await this.sendRequest(formUrl, formMethod, formData);
            await this.handleResponse(response, formMethod);
        } catch (error) {
            this.showErrors({ general: 'Something went wrong. Please try again or contact support.' });
        } finally {
            this.toggle();
        }
    },

    prepareFormData(formData, formMethod, data) {
        if (formMethod !== 'post' && formMethod !== 'get') {
            formData.append('_method', formMethod);
        }

        Object.entries(data).forEach(([key, value]) => formData.append(key, value));

        this.$el.querySelectorAll('input[disabled], input[readonly], select[disabled], textarea[disabled]')
            .forEach(input => formData.delete(input.name));
    },

    async sendRequest(url, method, formData) {
        return await fetch(url, {
            method: method === 'get' ? 'get' : 'post',
            headers: {
                'X-CSRF-TOKEN': this.token,
                'Accept': 'application/json'
            },
            body: formData
        });
    },

    async handleResponse(response, formMethod) {
        if (response.ok) {
            this.success = true;

            if (formMethod === 'post') {
                this.$el.reset();
            }

            $('.dataTable:visible').each((_, table) => {
                $(table).DataTable().ajax.reload();
            });

            const data = await response.json();
            window.toastr.success(data || 'Success!');

            dispatchEvent(new CustomEvent(`${name}:success`, { detail: { data, method: formMethod } }));
            dispatchEvent(new CustomEvent('form:global:success', { detail: { data, method: formMethod } }));
            return;
        }

        const { errors } = await response.json();
        this.showErrors(errors);
    },

    showErrors(errors) {
        this.success = false;
        this.errors = errors;
    },

    resetErrors() {
        this.success = false;
        this.errors = {};
    },

    toggle() {
        this.loading = !this.loading;
        this.$refs.button.disabled = this.loading;
    },

    destroy() {
        this.$el.removeEventListener('submit', this.submitHandler);
    }
});
