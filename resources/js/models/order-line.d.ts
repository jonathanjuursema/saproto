type OrderLine = {
    id: number;
    user_id: number | null;
    cashier_id: number | null;
    product_id: number;
    original_unit_price: number;
    units: number;
    total_price: number;
    authenticated_by: string;
    payed_with_cash: string /* Date */ | null;
    payed_with_bank_card: string /* Date */ | null;
    payed_with_mollie: number | null;
    payed_with_withdrawal: number | null;
    payed_with_loss: boolean;
    description: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
}
