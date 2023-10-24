declare namespace App.Models {
    type User = {
        id: number;
        name: string;
        calling_name: string;
        email: string;
        birthdate: string /* Date */ | null;
        phone: string | null;
        website: string | null;
        phone_visible: boolean;
        address_visible: boolean;
        receive_sms: boolean;
        keep_protube_history: boolean;
        show_birthday: boolean;
        show_achievements: boolean;
        profile_in_almanac: boolean;
        show_omnomcom_total: boolean;
        show_omnomcom_calories: boolean;
        keep_omnomcom_history: boolean;
        disable_omnomcom: boolean;
        theme: number;
        pref_calendar_alarm: boolean | null;
        pref_calendar_relevant_only: boolean;
        utwente_username: string | null;
        edu_username: string | null;
        utwente_department: string | null;
        did_study_create: boolean;
        did_study_itech: boolean;
        signed_nda: boolean;
        member?: Member | null;
        bank?: any | null;
        is_active_member?: boolean;
        completed_profile?: any;
        is_member?: boolean;
        signed_membership_form?: any;
        is_protube_admin?: any;
        photo_preview?: any;
        welcome_message?: string | any;
    }
}
