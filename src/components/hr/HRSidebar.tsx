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
  { title: "Statisztikák", url: "/hr/statistics", icon: BarChart3 },
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
                    <SidebarMenuButton 
                      isActive={isActive}
                      onClick={(e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        navigate(item.url);
                      }}
                      className="cursor-pointer"
                    >
                      <item.icon className="h-4 w-4" />
                      {open && <span>{item.title}</span>}
                    </SidebarMenuButton>
                  </SidebarMenuItem>
                );
              })}
              
              {/* Kijelentkezés gomb a menü alján */}
              <SidebarMenuItem>
                <SidebarMenuButton 
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    handleSignOut();
                  }} 
                  className="text-destructive hover:text-destructive cursor-pointer"
                >
                  <LogOut className="h-4 w-4" />
                  {open && <span>Kijelentkezés</span>}
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>
    </Sidebar>
  );
}
