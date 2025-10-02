import { QrCode, BarChart3, FileText, Settings as SettingsIcon, LogOut, Activity } from "lucide-react";
import { NavLink, useNavigate } from "react-router-dom";
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarTrigger,
  useSidebar,
} from "@/components/ui/sidebar";

const menuItems = [
  { title: "Audit Indítása", url: "/hr/create-audit", icon: QrCode },
  { title: "Futó Auditok", url: "/hr/running-audits", icon: Activity },
  { title: "Statisztikák", url: "/hr/statistics", icon: BarChart3 },
  { title: "Export & Jelentések", url: "/hr/export", icon: FileText },
  { title: "Beállítások", url: "/hr/settings", icon: SettingsIcon },
];

export function HRSidebar() {
  const { open } = useSidebar();
  const navigate = useNavigate();

  const handleSignOut = async () => {
    const { supabase } = await import("@/integrations/supabase/client");
    await supabase.auth.signOut();
    navigate("/");
  };

  return (
    <Sidebar collapsible="icon" className={open ? "w-60" : "w-14"}>
      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupLabel>HR Menü</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              {menuItems.map((item) => (
                <SidebarMenuItem key={item.title}>
                  <SidebarMenuButton asChild>
                    <NavLink 
                      to={item.url} 
                      end
                      className={({ isActive }) => 
                        isActive ? "bg-muted text-primary font-medium" : "hover:bg-muted/50"
                      }
                    >
                      <item.icon className="h-4 w-4" />
                      {open && <span>{item.title}</span>}
                    </NavLink>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              ))}
              
              {/* Kijelentkezés gomb a menü alján */}
              <SidebarMenuItem>
                <SidebarMenuButton onClick={handleSignOut} className="text-destructive hover:text-destructive">
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
