type PasswordEntry = {
    id: number;
    permission_id: number;
    description: string | null;
    username: string | null;
    password: string | null;
    url: string | null;
    note: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
};
