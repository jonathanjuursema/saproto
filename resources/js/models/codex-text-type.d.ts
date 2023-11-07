type CodexTextType = {
    id: number;
    type: string;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    texts?: CodexText[] | null;
}
