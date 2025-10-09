export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[]

export type Database = {
  // Allows to automatically instantiate createClient with right options
  // instead of createClient<Database, { PostgrestVersion: 'XX' }>(URL, KEY)
  __InternalSupabase: {
    PostgrestVersion: "13.0.5"
  }
  public: {
    Tables: {
      admin_approval_requests: {
        Row: {
          approval_token: string | null
          approved: boolean | null
          approved_at: string | null
          created_at: string
          email: string
          expires_at: string
          full_name: string
          id: string
          pending_verification: boolean | null
          user_id: string
        }
        Insert: {
          approval_token?: string | null
          approved?: boolean | null
          approved_at?: string | null
          created_at?: string
          email: string
          expires_at: string
          full_name: string
          id?: string
          pending_verification?: boolean | null
          user_id: string
        }
        Update: {
          approval_token?: string | null
          approved?: boolean | null
          approved_at?: string | null
          created_at?: string
          email?: string
          expires_at?: string
          full_name?: string
          id?: string
          pending_verification?: boolean | null
          user_id?: string
        }
        Relationships: []
      }
      admin_verification_codes: {
        Row: {
          code: string
          created_at: string
          email: string
          expires_at: string
          id: string
          used: boolean | null
          used_at: string | null
        }
        Insert: {
          code: string
          created_at?: string
          email: string
          expires_at: string
          id?: string
          used?: boolean | null
          used_at?: string | null
        }
        Update: {
          code?: string
          created_at?: string
          email?: string
          expires_at?: string
          id?: string
          used?: boolean | null
          used_at?: string | null
        }
        Relationships: []
      }
      audit_responses: {
        Row: {
          audit_id: string
          employee_metadata: Json | null
          id: string
          responses: Json
          submitted_at: string
        }
        Insert: {
          audit_id: string
          employee_metadata?: Json | null
          id?: string
          responses?: Json
          submitted_at?: string
        }
        Update: {
          audit_id?: string
          employee_metadata?: Json | null
          id?: string
          responses?: Json
          submitted_at?: string
        }
        Relationships: [
          {
            foreignKeyName: "audit_responses_audit_id_fkey"
            columns: ["audit_id"]
            isOneToOne: false
            referencedRelation: "audits"
            referencedColumns: ["id"]
          },
        ]
      }
      audits: {
        Row: {
          access_mode: string | null
          access_token: string
          available_languages: string[] | null
          communication_text: string | null
          created_at: string
          custom_colors: Json | null
          eap_program_url: string | null
          email_template: Json | null
          expires_at: string | null
          hr_user_id: string
          id: string
          is_active: boolean | null
          logo_url: string | null
          program_name: string | null
          questionnaire_id: string
          recurrence_config: Json | null
          start_date: string | null
          updated_at: string
        }
        Insert: {
          access_mode?: string | null
          access_token: string
          available_languages?: string[] | null
          communication_text?: string | null
          created_at?: string
          custom_colors?: Json | null
          eap_program_url?: string | null
          email_template?: Json | null
          expires_at?: string | null
          hr_user_id: string
          id?: string
          is_active?: boolean | null
          logo_url?: string | null
          program_name?: string | null
          questionnaire_id: string
          recurrence_config?: Json | null
          start_date?: string | null
          updated_at?: string
        }
        Update: {
          access_mode?: string | null
          access_token?: string
          available_languages?: string[] | null
          communication_text?: string | null
          created_at?: string
          custom_colors?: Json | null
          eap_program_url?: string | null
          email_template?: Json | null
          expires_at?: string | null
          hr_user_id?: string
          id?: string
          is_active?: boolean | null
          logo_url?: string | null
          program_name?: string | null
          questionnaire_id?: string
          recurrence_config?: Json | null
          start_date?: string | null
          updated_at?: string
        }
        Relationships: [
          {
            foreignKeyName: "audits_questionnaire_id_fkey"
            columns: ["questionnaire_id"]
            isOneToOne: false
            referencedRelation: "questionnaires"
            referencedColumns: ["id"]
          },
        ]
      }
      companies: {
        Row: {
          company_name: string
          contact_email: string
          contact_phone: string | null
          created_at: string
          employee_count: string | null
          id: string
          industry: string | null
          notes: string | null
          subscription_end_date: string | null
          subscription_package: string | null
          subscription_start_date: string | null
          subscription_status: string | null
          updated_at: string
        }
        Insert: {
          company_name: string
          contact_email: string
          contact_phone?: string | null
          created_at?: string
          employee_count?: string | null
          id?: string
          industry?: string | null
          notes?: string | null
          subscription_end_date?: string | null
          subscription_package?: string | null
          subscription_start_date?: string | null
          subscription_status?: string | null
          updated_at?: string
        }
        Update: {
          company_name?: string
          contact_email?: string
          contact_phone?: string | null
          created_at?: string
          employee_count?: string | null
          id?: string
          industry?: string | null
          notes?: string | null
          subscription_end_date?: string | null
          subscription_package?: string | null
          subscription_start_date?: string | null
          subscription_status?: string | null
          updated_at?: string
        }
        Relationships: []
      }
      email_verifications: {
        Row: {
          created_at: string | null
          email: string
          expires_at: string
          id: string
          token: string
          verified: boolean | null
        }
        Insert: {
          created_at?: string | null
          email: string
          expires_at: string
          id?: string
          token: string
          verified?: boolean | null
        }
        Update: {
          created_at?: string | null
          email?: string
          expires_at?: string
          id?: string
          token?: string
          verified?: boolean | null
        }
        Relationships: []
      }
      export_history: {
        Row: {
          audit_id: string
          audit_name: string
          created_at: string
          file_name: string
          file_type: string
          id: string
          user_id: string
        }
        Insert: {
          audit_id: string
          audit_name: string
          created_at?: string
          file_name: string
          file_type: string
          id?: string
          user_id: string
        }
        Update: {
          audit_id?: string
          audit_name?: string
          created_at?: string
          file_name?: string
          file_type?: string
          id?: string
          user_id?: string
        }
        Relationships: [
          {
            foreignKeyName: "export_history_audit_id_fkey"
            columns: ["audit_id"]
            isOneToOne: false
            referencedRelation: "audits"
            referencedColumns: ["id"]
          },
        ]
      }
      magazine_articles: {
        Row: {
          author: string
          category: string
          content: string
          created_at: string
          excerpt: string
          id: string
          image_url: string | null
          is_featured: boolean | null
          is_published: boolean | null
          published_date: string
          slug: string
          title: string
          updated_at: string
          view_count: number
        }
        Insert: {
          author: string
          category?: string
          content: string
          created_at?: string
          excerpt: string
          id?: string
          image_url?: string | null
          is_featured?: boolean | null
          is_published?: boolean | null
          published_date?: string
          slug: string
          title: string
          updated_at?: string
          view_count?: number
        }
        Update: {
          author?: string
          category?: string
          content?: string
          created_at?: string
          excerpt?: string
          id?: string
          image_url?: string | null
          is_featured?: boolean | null
          is_published?: boolean | null
          published_date?: string
          slug?: string
          title?: string
          updated_at?: string
          view_count?: number
        }
        Relationships: []
      }
      newsletter_campaigns: {
        Row: {
          content: string
          created_at: string
          failed_count: number | null
          id: string
          recipient_count: number
          sent_at: string
          sent_by: string
          sent_count: number | null
          status: string
          subject: string
          template_id: string | null
        }
        Insert: {
          content: string
          created_at?: string
          failed_count?: number | null
          id?: string
          recipient_count?: number
          sent_at?: string
          sent_by: string
          sent_count?: number | null
          status?: string
          subject: string
          template_id?: string | null
        }
        Update: {
          content?: string
          created_at?: string
          failed_count?: number | null
          id?: string
          recipient_count?: number
          sent_at?: string
          sent_by?: string
          sent_count?: number | null
          status?: string
          subject?: string
          template_id?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "newsletter_campaigns_template_id_fkey"
            columns: ["template_id"]
            isOneToOne: false
            referencedRelation: "newsletter_templates"
            referencedColumns: ["id"]
          },
        ]
      }
      newsletter_subscribers: {
        Row: {
          created_at: string
          email: string
          id: string
          is_active: boolean | null
          name: string | null
          source: string | null
          subscribed_at: string
          unsubscribe_token: string | null
        }
        Insert: {
          created_at?: string
          email: string
          id?: string
          is_active?: boolean | null
          name?: string | null
          source?: string | null
          subscribed_at?: string
          unsubscribe_token?: string | null
        }
        Update: {
          created_at?: string
          email?: string
          id?: string
          is_active?: boolean | null
          name?: string | null
          source?: string | null
          subscribed_at?: string
          unsubscribe_token?: string | null
        }
        Relationships: []
      }
      newsletter_templates: {
        Row: {
          background_color: string
          content_button_color: string | null
          content_button_shadow_color: string | null
          content_button_text: string | null
          content_button_text_color: string | null
          content_button_url: string | null
          content_text_color: string | null
          content_text_size: string | null
          created_at: string
          created_by: string | null
          extra_button_color: string | null
          extra_button_shadow_color: string | null
          extra_button_text: string | null
          extra_button_text_color: string | null
          extra_button_url: string | null
          extra_content: string | null
          extra_content_bg_color: string
          extra_content_border_color: string
          extra_content_text_color: string | null
          extra_content_text_size: string | null
          featured_image_url: string | null
          footer_address: string | null
          footer_color: string
          footer_company: string
          footer_copyright_color: string | null
          footer_copyright_text: string | null
          footer_gradient: string | null
          footer_link_color: string
          footer_links: Json | null
          footer_logo_url: string | null
          footer_text: string
          greeting_bg_color: string | null
          greeting_text: string
          greeting_text_color: string | null
          header_color: string
          header_color_1: string
          header_color_2: string
          header_gradient_1: string | null
          header_gradient_2: string | null
          header_subtitle: string | null
          header_title: string
          id: string
          is_default: boolean | null
          logo_url: string | null
          name: string
          primary_color: string
          sender_email: string
          sender_name: string
          show_content_button: boolean | null
          show_extra_button: boolean | null
          updated_at: string
        }
        Insert: {
          background_color?: string
          content_button_color?: string | null
          content_button_shadow_color?: string | null
          content_button_text?: string | null
          content_button_text_color?: string | null
          content_button_url?: string | null
          content_text_color?: string | null
          content_text_size?: string | null
          created_at?: string
          created_by?: string | null
          extra_button_color?: string | null
          extra_button_shadow_color?: string | null
          extra_button_text?: string | null
          extra_button_text_color?: string | null
          extra_button_url?: string | null
          extra_content?: string | null
          extra_content_bg_color?: string
          extra_content_border_color?: string
          extra_content_text_color?: string | null
          extra_content_text_size?: string | null
          featured_image_url?: string | null
          footer_address?: string | null
          footer_color?: string
          footer_company?: string
          footer_copyright_color?: string | null
          footer_copyright_text?: string | null
          footer_gradient?: string | null
          footer_link_color?: string
          footer_links?: Json | null
          footer_logo_url?: string | null
          footer_text?: string
          greeting_bg_color?: string | null
          greeting_text?: string
          greeting_text_color?: string | null
          header_color?: string
          header_color_1?: string
          header_color_2?: string
          header_gradient_1?: string | null
          header_gradient_2?: string | null
          header_subtitle?: string | null
          header_title?: string
          id?: string
          is_default?: boolean | null
          logo_url?: string | null
          name: string
          primary_color?: string
          sender_email?: string
          sender_name?: string
          show_content_button?: boolean | null
          show_extra_button?: boolean | null
          updated_at?: string
        }
        Update: {
          background_color?: string
          content_button_color?: string | null
          content_button_shadow_color?: string | null
          content_button_text?: string | null
          content_button_text_color?: string | null
          content_button_url?: string | null
          content_text_color?: string | null
          content_text_size?: string | null
          created_at?: string
          created_by?: string | null
          extra_button_color?: string | null
          extra_button_shadow_color?: string | null
          extra_button_text?: string | null
          extra_button_text_color?: string | null
          extra_button_url?: string | null
          extra_content?: string | null
          extra_content_bg_color?: string
          extra_content_border_color?: string
          extra_content_text_color?: string | null
          extra_content_text_size?: string | null
          featured_image_url?: string | null
          footer_address?: string | null
          footer_color?: string
          footer_company?: string
          footer_copyright_color?: string | null
          footer_copyright_text?: string | null
          footer_gradient?: string | null
          footer_link_color?: string
          footer_links?: Json | null
          footer_logo_url?: string | null
          footer_text?: string
          greeting_bg_color?: string | null
          greeting_text?: string
          greeting_text_color?: string | null
          header_color?: string
          header_color_1?: string
          header_color_2?: string
          header_gradient_1?: string | null
          header_gradient_2?: string | null
          header_subtitle?: string | null
          header_title?: string
          id?: string
          is_default?: boolean | null
          logo_url?: string | null
          name?: string
          primary_color?: string
          sender_email?: string
          sender_name?: string
          show_content_button?: boolean | null
          show_extra_button?: boolean | null
          updated_at?: string
        }
        Relationships: []
      }
      notification_emails: {
        Row: {
          created_at: string
          email: string
          id: string
          is_verified: boolean | null
          user_id: string
        }
        Insert: {
          created_at?: string
          email: string
          id?: string
          is_verified?: boolean | null
          user_id: string
        }
        Update: {
          created_at?: string
          email?: string
          id?: string
          is_verified?: boolean | null
          user_id?: string
        }
        Relationships: []
      }
      profiles: {
        Row: {
          address: string | null
          billing_address: string | null
          billing_address_same_as_company: boolean | null
          billing_city: string | null
          billing_cycle: string | null
          billing_postal_code: string | null
          city: string | null
          company_domain: string | null
          company_name: string | null
          contact_phone: string | null
          country: string | null
          created_at: string
          data_retention_days: number | null
          email: string | null
          employee_count: string | null
          full_name: string | null
          id: string
          industry: string | null
          postal_code: string | null
          preferred_languages: string[] | null
          selected_package: string | null
          updated_at: string
          vat_id: string | null
        }
        Insert: {
          address?: string | null
          billing_address?: string | null
          billing_address_same_as_company?: boolean | null
          billing_city?: string | null
          billing_cycle?: string | null
          billing_postal_code?: string | null
          city?: string | null
          company_domain?: string | null
          company_name?: string | null
          contact_phone?: string | null
          country?: string | null
          created_at?: string
          data_retention_days?: number | null
          email?: string | null
          employee_count?: string | null
          full_name?: string | null
          id: string
          industry?: string | null
          postal_code?: string | null
          preferred_languages?: string[] | null
          selected_package?: string | null
          updated_at?: string
          vat_id?: string | null
        }
        Update: {
          address?: string | null
          billing_address?: string | null
          billing_address_same_as_company?: boolean | null
          billing_city?: string | null
          billing_cycle?: string | null
          billing_postal_code?: string | null
          city?: string | null
          company_domain?: string | null
          company_name?: string | null
          contact_phone?: string | null
          country?: string | null
          created_at?: string
          data_retention_days?: number | null
          email?: string | null
          employee_count?: string | null
          full_name?: string | null
          id?: string
          industry?: string | null
          postal_code?: string | null
          preferred_languages?: string[] | null
          selected_package?: string | null
          updated_at?: string
          vat_id?: string | null
        }
        Relationships: []
      }
      questionnaires: {
        Row: {
          created_at: string
          description: string | null
          id: string
          is_active: boolean | null
          questions: Json
          title: string
          updated_at: string
        }
        Insert: {
          created_at?: string
          description?: string | null
          id?: string
          is_active?: boolean | null
          questions?: Json
          title: string
          updated_at?: string
        }
        Update: {
          created_at?: string
          description?: string | null
          id?: string
          is_active?: boolean | null
          questions?: Json
          title?: string
          updated_at?: string
        }
        Relationships: []
      }
      user_invitations: {
        Row: {
          accepted: boolean | null
          company_id: string
          created_at: string
          email: string
          expires_at: string
          id: string
          invited_by: string
          role: Database["public"]["Enums"]["app_role"]
          token: string
        }
        Insert: {
          accepted?: boolean | null
          company_id: string
          created_at?: string
          email: string
          expires_at: string
          id?: string
          invited_by: string
          role?: Database["public"]["Enums"]["app_role"]
          token: string
        }
        Update: {
          accepted?: boolean | null
          company_id?: string
          created_at?: string
          email?: string
          expires_at?: string
          id?: string
          invited_by?: string
          role?: Database["public"]["Enums"]["app_role"]
          token?: string
        }
        Relationships: []
      }
      user_roles: {
        Row: {
          created_at: string
          id: string
          role: Database["public"]["Enums"]["app_role"]
          user_id: string
        }
        Insert: {
          created_at?: string
          id?: string
          role: Database["public"]["Enums"]["app_role"]
          user_id: string
        }
        Update: {
          created_at?: string
          id?: string
          role?: Database["public"]["Enums"]["app_role"]
          user_id?: string
        }
        Relationships: []
      }
    }
    Views: {
      [_ in never]: never
    }
    Functions: {
      generate_access_token: {
        Args: Record<PropertyKey, never>
        Returns: string
      }
      get_audit_for_survey: {
        Args: { _access_token: string }
        Returns: {
          available_languages: string[]
          communication_text: string
          custom_colors: Json
          eap_program_url: string
          expires_at: string
          id: string
          is_active: boolean
          logo_url: string
          program_name: string
          questionnaire_id: string
        }[]
      }
      get_company_users: {
        Args: { company_name_param: string }
        Returns: {
          email: string
          full_name: string
          role: Database["public"]["Enums"]["app_role"]
          user_id: string
        }[]
      }
      get_user_role: {
        Args: { _user_id: string }
        Returns: Database["public"]["Enums"]["app_role"]
      }
      has_role: {
        Args: {
          _role: Database["public"]["Enums"]["app_role"]
          _user_id: string
        }
        Returns: boolean
      }
      increment_article_view_count: {
        Args: { _article_id: string }
        Returns: undefined
      }
      same_company_as_user: {
        Args: { _profile_id: string; _user_id: string }
        Returns: boolean
      }
      user_owns_audit: {
        Args: { _audit_id: string; _user_id: string }
        Returns: boolean
      }
      verify_email_token: {
        Args: { _token: string }
        Returns: {
          email: string
          expired: boolean
          verified: boolean
        }[]
      }
      verify_email_with_token: {
        Args: { _token: string }
        Returns: Json
      }
    }
    Enums: {
      app_role: "admin" | "hr"
    }
    CompositeTypes: {
      [_ in never]: never
    }
  }
}

type DatabaseWithoutInternals = Omit<Database, "__InternalSupabase">

type DefaultSchema = DatabaseWithoutInternals[Extract<keyof Database, "public">]

export type Tables<
  DefaultSchemaTableNameOrOptions extends
    | keyof (DefaultSchema["Tables"] & DefaultSchema["Views"])
    | { schema: keyof DatabaseWithoutInternals },
  TableName extends DefaultSchemaTableNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof (DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"] &
        DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Views"])
    : never = never,
> = DefaultSchemaTableNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? (DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"] &
      DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Views"])[TableName] extends {
      Row: infer R
    }
    ? R
    : never
  : DefaultSchemaTableNameOrOptions extends keyof (DefaultSchema["Tables"] &
        DefaultSchema["Views"])
    ? (DefaultSchema["Tables"] &
        DefaultSchema["Views"])[DefaultSchemaTableNameOrOptions] extends {
        Row: infer R
      }
      ? R
      : never
    : never

export type TablesInsert<
  DefaultSchemaTableNameOrOptions extends
    | keyof DefaultSchema["Tables"]
    | { schema: keyof DatabaseWithoutInternals },
  TableName extends DefaultSchemaTableNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = DefaultSchemaTableNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Insert: infer I
    }
    ? I
    : never
  : DefaultSchemaTableNameOrOptions extends keyof DefaultSchema["Tables"]
    ? DefaultSchema["Tables"][DefaultSchemaTableNameOrOptions] extends {
        Insert: infer I
      }
      ? I
      : never
    : never

export type TablesUpdate<
  DefaultSchemaTableNameOrOptions extends
    | keyof DefaultSchema["Tables"]
    | { schema: keyof DatabaseWithoutInternals },
  TableName extends DefaultSchemaTableNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = DefaultSchemaTableNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Update: infer U
    }
    ? U
    : never
  : DefaultSchemaTableNameOrOptions extends keyof DefaultSchema["Tables"]
    ? DefaultSchema["Tables"][DefaultSchemaTableNameOrOptions] extends {
        Update: infer U
      }
      ? U
      : never
    : never

export type Enums<
  DefaultSchemaEnumNameOrOptions extends
    | keyof DefaultSchema["Enums"]
    | { schema: keyof DatabaseWithoutInternals },
  EnumName extends DefaultSchemaEnumNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[DefaultSchemaEnumNameOrOptions["schema"]]["Enums"]
    : never = never,
> = DefaultSchemaEnumNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[DefaultSchemaEnumNameOrOptions["schema"]]["Enums"][EnumName]
  : DefaultSchemaEnumNameOrOptions extends keyof DefaultSchema["Enums"]
    ? DefaultSchema["Enums"][DefaultSchemaEnumNameOrOptions]
    : never

export type CompositeTypes<
  PublicCompositeTypeNameOrOptions extends
    | keyof DefaultSchema["CompositeTypes"]
    | { schema: keyof DatabaseWithoutInternals },
  CompositeTypeName extends PublicCompositeTypeNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[PublicCompositeTypeNameOrOptions["schema"]]["CompositeTypes"]
    : never = never,
> = PublicCompositeTypeNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[PublicCompositeTypeNameOrOptions["schema"]]["CompositeTypes"][CompositeTypeName]
  : PublicCompositeTypeNameOrOptions extends keyof DefaultSchema["CompositeTypes"]
    ? DefaultSchema["CompositeTypes"][PublicCompositeTypeNameOrOptions]
    : never

export const Constants = {
  public: {
    Enums: {
      app_role: ["admin", "hr"],
    },
  },
} as const
