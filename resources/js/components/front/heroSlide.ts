export interface HeroSlideData {
    id?: number;
    image_url?: string | null;
    title?: string | null;
    subtitle?: string | null;
    text?: string | null;
    button1_label?: string | null;
    button1_url?: string | null;
    button2_label?: string | null;
    button2_url?: string | null;
    text_position?: 'left' | 'center' | 'right' | string | null;
    overlay_opacity?: number | null;
}
