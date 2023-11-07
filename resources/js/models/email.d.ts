type Email = {
    id: number;
    description: string;
    subject: string;
    sender_name: string;
    sender_address: string;
    body: string;
    to_user: boolean;
    to_member: boolean;
    to_pending: boolean;
    to_list: boolean;
    to_event: boolean;
    to_backup: boolean;
    to_active: boolean;
    sent_to: number | null;
    sent: boolean;
    ready: boolean;
    time: number;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
}
