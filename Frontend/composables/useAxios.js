// import axios from "axios";

// export default function useAxios() {
//     const config = useRuntimeConfig();
//     const token = useState('auth-token').value || localStorage.getItem('auth-token');

//     const api = axios.create({
//         baseURL: config.public.apiBase,
//         headers: {
//             "Content-Type": "application/json",
//             Accept: "application/json",
//             ...(token && { Authorization: `Bearer ${token}` }),
//         },
//         withCredentials: true,
//         withXSRFToken: true,
//     })

//     return api;
// }


import axios from "axios";

export default function useAxios() {
    const config = useRuntimeConfig();
    let token = useState('auth-token').value;

    if (process.client) { // chỉ chạy khi ở client
        token = token || localStorage.getItem('auth-token');
    }

    const api = axios.create({
        baseURL: config.public.apiBase,
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            ...(token && { Authorization: `Bearer ${token}` }),
        },
        withCredentials: true,
        withXSRFToken: true,
    });

    return api;
}
