type CodexText = {
    id: number;
    type_id: number;
    name: string;
    text: string;
    created_at: string /* Date */ | null;
    updated_at: string /* Date */ | null;
    type?: CodexTextType | null;
    codices?: Codex[] | null;
}
