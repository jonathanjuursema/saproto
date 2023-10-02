type QrAuthRequest = {
    id: number;
    user_id: number;
    auth_token: string;
    qr_token: string;
    description: string;
    approved_at: string /* Date */ | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
};
