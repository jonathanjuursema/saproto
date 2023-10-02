type Joboffer = {
    id: number;
    company_id: number;
    title: string;
    description: string | null;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    redirect_url: string | null;
};
