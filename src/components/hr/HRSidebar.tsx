import { Home, QrCode, BarChart3, FileText, Settings as SettingsIcon, LogOut, ClipboardList } from "lucide-react";
import { NavLink, useNavigate, useLocation } from "react-router-dom";
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from "@/components/ui/sidebar";

const menuItems = [
  { title: "Dashboard", url: "/hr", icon: Home },
  { title: "EAP Pulse", url: "/hr/eap-audit", icon: QrCode },
  { title: "Egyedi közvélemény-kutatás", url: "/hr/custom-survey", icon: ClipboardList },
  { title: "Riportok", url: "/hr/statistics", icon: BarChart3 },
  { title: "Export & Jelentések", url: "/hr/export", icon: FileText },
  { title: "Beállítások", url: "/hr/settings", icon: SettingsIcon },
];

export function HRSidebar() {
  const { open } = useSidebar();
  const navigate = useNavigate();
  const location = useLocation();

  const handleSignOut = async () => {
    const { supabase } = await import("@/integrations/supabase/client");
    await supabase.auth.signOut();
    navigate("/");
  };

  // Check if EAP Audit routes are active
  const isEAPAuditActive = 
    location.pathname.includes('/hr/eap-audit') ||
    location.pathname === '/hr/create-audit' ||
    location.pathname === '/hr/running-audits' ||
    location.pathname === '/hr/audit-questionnaire';

  return (
    <Sidebar collapsible="icon" className={open ? "w-60" : "w-14"}>
      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupLabel>HR Menü</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              {menuItems.map((item) => {
                // Special handling for EAP Audit to show active state for sub-routes
                const isActive = item.url === '/hr/eap-audit' 
                  ? isEAPAuditActive 
                  : location.pathname === item.url;
                
                return (
                  <SidebarMenuItem key={item.title}>
                    <SidebarMenuButton asChild isActive={isActive}>
                      <button
                        type="button"
                        onClick={() => {
                          console.log('Navigating to:', item.url);
                          navigate(item.url);
                        }}
                      >
                        <item.icon className="h-4 w-4 shrink-0" />
                        {open && <span>{item.title}</span>}
                      </button>
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                );
              })}
              
              {/* Kijelentkezés gomb a menü alján */}
              <SidebarMenuItem>
                <SidebarMenuButton asChild>
                  <button
                    type="button"
                    onClick={() => {
                      console.log('Signing out');
                      handleSignOut();
                    }}
                    className="text-destructive hover:text-destructive"
                  >
                    <LogOut className="h-4 w-4 shrink-0" />
                    {open && <span>Kijelentkezés</span>}
                  </button>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>
    </Sidebar>
  );
}
