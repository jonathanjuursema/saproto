type TicketPurchase = {
    id: number;
    ticket_id: number;
    orderline_id: number;
    user_id: number;
    barcode: string;
    scanned: string /* Date */ | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    payment_complete: boolean;
    api_attributes?: any;
};
