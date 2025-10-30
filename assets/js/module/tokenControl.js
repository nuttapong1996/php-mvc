import { getCsrfToken} from "./utils.js";

export async function refreshAccessToken() {
    try {
        const response = await fetch('auth/refresh', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }

        });

        if (!response.ok) {
            console.warn("Refresh failed with status:", response.status);
            return response.status;
        }

        console.info('Token refreshed!')

    } catch (error) {
        console.warn('No token found , please login')
    }
}

export async function renewRefreshToken() {

    try {
        const response = await fetch('auth/renew', {
            method: 'POST',
            credentials: 'include',
        });

        if (!response.ok) {
            console.warn("Renew failed with status:", response.status);
            return response.status;
        }
        const data = await response.json();

        if (data.code === 200) {
            console.info('Token renewed!');
            return 200;
        } else {
            console.warn("Renew failed:", data);
            return data.code || 401;
        }

    } catch (error) {
        console.warn('No token found , please login');
        return 500;
    }
}