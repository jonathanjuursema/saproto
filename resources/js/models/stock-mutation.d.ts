type StockMutation = {
    id: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    user_id: number;
    product_id: number;
    before: number;
    after: number;
    is_bulk: boolean;
    product?: Product | null;
    user?: User | null;
}
