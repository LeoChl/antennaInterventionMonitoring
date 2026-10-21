const TOKEN_KEY = 'api_token';

export const getTokenFromStore = () => sessionStorage.getItem(TOKEN_KEY);

export const clearToken = () => sessionStorage.removeItem(TOKEN_KEY);

export const ensureToken = () => {
    let token = getTokenFromStore();
    if (!token) {
        token = window.prompt('API token:') ?? '';
        if (token) {
            sessionStorage.setItem(TOKEN_KEY, token);
        }
    }
    return token;
};

const defaultOptions = () => ({
    headers: {
        Authorization: `Bearer ${ensureToken()}`,
        Accept: 'application/json',
    },
});

export default defaultOptions;
