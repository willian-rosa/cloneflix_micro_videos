export interface ListResponse<T>{
    data: T[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
    links: {
        fist: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
}

interface Timestampable {
    readonly created_at: string;
    readonly deleted_at: string | null;
    readonly updated_at: string;
}

export interface Category extends Timestampable {
    readonly id: string;
    name: string;
    description: string;
    is_active: boolean;
}