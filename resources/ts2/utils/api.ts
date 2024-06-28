import { ofetch } from 'ofetch';

export const $api = ofetch.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  async onRequest({ options }) {
    const accessToken = useCookie('accessToken').value;
    const csrfToken = useCookie('XSRF-TOKEN').value;

    if (accessToken) {
      options.headers = {
        ...options.headers,
        Authorization: `Bearer ${accessToken}`,
      };
    }

    if (csrfToken) {
      options.headers = {
        ...options.headers,
        'X-XSRF-TOKEN': csrfToken,
      };
    }
  },
});
