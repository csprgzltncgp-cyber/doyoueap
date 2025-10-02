import { QrCode, BarChart3, FileText, Settings as SettingsIcon, LogOut, Activity, FileQuestion, ClipboardList, ChevronRight } from "lucide-react";
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
  SidebarMenuSub,
  SidebarMenuSubItem,
  SidebarMenuSubButton,
  SidebarTrigger,
  useSidebar,
} from "@/components/ui/sidebar";
import {
  Collapsible,
  CollapsibleTrigger,
  CollapsibleContent,
} from "@/components/ui/collapsible";

const auditMenuItems = [
  { title: "Audit Indítása", url: "/hr/create-audit", icon: QrCode },
  { title: "Futó Auditok", url: "/hr/running-audits", icon: Activity },
  { title: "Demo Audit", url: "/hr/audit-questionnaire", icon: FileQuestion },
];

const otherMenuItems = [
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

  // Check if any audit route is active
  const isAuditActive = auditMenuItems.some(item => location.pathname === item.url);

  return (
    <Sidebar collapsible="icon" className={open ? "w-60" : "w-14"}>
      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupLabel>HR Menü</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              {/* EAP Audit collapsible group */}
              <Collapsible defaultOpen={isAuditActive} className="group/collapsible">
                <SidebarMenuItem>
                  <CollapsibleTrigger asChild>
                    <SidebarMenuButton>
                      <QrCode className="h-4 w-4" />
                      {open && <span>EAP Audit</span>}
                      {open && (
                        <ChevronRight className="ml-auto h-4 w-4 transition-transform group-data-[state=open]/collapsible:rotate-90" />
                      )}
                    </SidebarMenuButton>
                  </CollapsibleTrigger>
                  <CollapsibleContent>
                    <SidebarMenuSub>
                      {auditMenuItems.map((item) => (
                        <SidebarMenuSubItem key={item.title}>
                          <SidebarMenuSubButton asChild>
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
                          </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                      ))}
                    </SidebarMenuSub>
                  </CollapsibleContent>
                </SidebarMenuItem>
              </Collapsible>

              {/* Other menu items */}
              {otherMenuItems.map((item) => (
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
