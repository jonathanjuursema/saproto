type FeedbackCategory = {
    id: number;
    title: string;
    url: string;
    review: boolean;
    reviewer_id: number | null;
    can_reply: boolean;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    feedback?: Feedback[] | null;
    reviewer?: User | null;
}
