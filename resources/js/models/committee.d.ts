type Committee = {
    id: number;
    name: string;
    slug: string;
    description: string;
    public: boolean;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    allow_anonymous_email: boolean;
    is_society: boolean;
    email_address?: any;
}
