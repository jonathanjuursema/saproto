type DinnerformOrderline = {
    id: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    user_id: number;
    dinnerform_id: number;
    description: string;
    price: number;
    helper: boolean;
    closed: boolean;
    price_with_discount?: any;
};
