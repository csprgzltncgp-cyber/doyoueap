import { 
  QrCode, 
  BarChart3, 
  FileText, 
  Settings as SettingsIcon, 
  LogOut,
  Eye,
  Shield,
  Activity,
  Target,
  Heart,
  Users,
  TrendingUp,
  GitCompare
} from "lucide-react";
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

const menuItems = [
  { title: "Audit Indítása", url: "/hr/create-audit", icon: QrCode },
  { title: "Statisztikák", url: "/hr/statistics", icon: BarChart3 },
  { title: "Export & Jelentések", url: "/hr/export", icon: FileText },
  { title: "Beállítások", url: "/hr/settings", icon: SettingsIcon },
];

const statisticsSubItems = [
  { title: "Ismertség", value: "awareness", icon: Eye },
  { title: "Bizalom & Hajlandóság", value: "trust", icon: Shield },
  { title: "Használat", value: "usage", icon: Activity },
  { title: "Hatás", value: "impact", icon: Target },
  { title: "Motiváció", value: "motivation", icon: Heart },
  { title: "Demográfia", value: "demographics", icon: Users },
  { title: "Trendek", value: "trends", icon: TrendingUp },
  { title: "Összehasonlítás", value: "compare", icon: GitCompare },
];

export function HRSidebar() {
  const { open } = useSidebar();
  const navigate = useNavigate();
  const location = useLocation();
  const isStatisticsPage = location.pathname === "/hr/statistics";

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
                item.title === "Statisztikák" ? (
                  <Collapsible key={item.title} defaultOpen={isStatisticsPage}>
                    <SidebarMenuItem>
                      <CollapsibleTrigger asChild>
                        <SidebarMenuButton
                          className={
                            isStatisticsPage ? "bg-muted text-primary font-medium" : "hover:bg-muted/50"
                          }
                        >
                          <item.icon className="h-4 w-4" />
                          {open && <span>{item.title}</span>}
                        </SidebarMenuButton>
                      </CollapsibleTrigger>
                      <CollapsibleContent>
                        <SidebarMenuSub>
                          {statisticsSubItems.map((subItem) => {
                            const searchParams = new URLSearchParams(location.search);
                            const currentTab = searchParams.get('tab') || 'awareness';
                            const isTabActive = isStatisticsPage && currentTab === subItem.value;
                            
                            return (
                              <SidebarMenuSubItem key={subItem.value}>
                                <SidebarMenuSubButton asChild>
                                  <NavLink
                                    to={`${item.url}?tab=${subItem.value}`}
                                    className={
                                      isTabActive ? "bg-muted text-primary font-medium" : "hover:bg-muted/50"
                                    }
                                  >
                                    <subItem.icon className="h-4 w-4" />
                                    {open && <span>{subItem.title}</span>}
                                  </NavLink>
                                </SidebarMenuSubButton>
                              </SidebarMenuSubItem>
                            );
                          })}
                        </SidebarMenuSub>
                      </CollapsibleContent>
                    </SidebarMenuItem>
                  </Collapsible>
                ) : (
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
                )
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
