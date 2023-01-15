import {AxiosInstance, AxiosRequestConfig, AxiosResponse, CancelTokenSource} from "axios";
import axios from "axios";

export default class HttpResource {
    private cancelList: CancelTokenSource | null = null;
    constructor(protected http: AxiosInstance, protected resource: string) {
    }

    list<T = any>(options?: { queryParams? }): Promise<AxiosResponse<T>> {
        if (this.cancelList) {
            this.cancelList.cancel('List request cancelled.');
        }

        this.cancelList = axios.CancelToken.source();

        const config: AxiosRequestConfig = {
            cancelToken: this.cancelList.token
        };

        if (options && options.queryParams) {
            config.params = options.queryParams;
        }
        return this.http.get<T>(this.resource, config);
    }

    get<T = any>(id: any): Promise<AxiosResponse<T>> {
        return this.http.get<T>(`${this.resource}/${id}`);
    }

    create<T = any>(data): Promise<AxiosResponse<T>> {
        return this.http.post<T>(this.resource, data);
    }

    update<T = any>(id: any, data: any): Promise<AxiosResponse<T>> {
        return this.http.put(this.resource + '/' + id, data);
    }

    delete<T = any>(id): Promise<AxiosResponse<T>> {
        return this.http.delete<T>(this.resource + '/' + id);
    }

    isCancelledRequest(error) {
        return axios.isCancel(error);
    }
}
