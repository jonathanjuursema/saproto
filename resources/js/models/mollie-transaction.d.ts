type MollieTransaction = {
    id: number;
    user_id: string;
    mollie_id: string;
    status: string;
    amount: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    payment_url: string | null;
};
