type Product = {
    id: number;
    account_id: number;
    image_id: number | null;
    name: string;
    price: number;
    calories: number;
    supplier_id: string | null;
    stock: number;
    preferred_stock: number;
    max_stock: number;
    supplier_collo: number;
    is_visible: boolean;
    is_alcoholic: boolean;
    is_visible_when_no_stock: boolean;
    image_url?: any;
}
