import { LayoutDashboard, Users, Eye, Shield, TrendingUp, BarChart3, Target, Calendar, FileText, GitCompare, Settings as SettingsIcon, QrCode } from "lucide-react";
import { NavLink } from "react-router-dom";
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
  { title: "Fő Dashboard", url: "/hr", icon: LayoutDashboard },
  { title: "User Kategóriák", url: "/hr/categories", icon: Users },
  { title: "Awareness", url: "/hr/awareness", icon: Eye },
  { title: "Trust & Willingness", url: "/hr/trust", icon: Shield },
  { title: "Usage", url: "/hr/usage", icon: TrendingUp },
  { title: "Impact", url: "/hr/impact", icon: BarChart3 },
  { title: "Motiváció", url: "/hr/motivation", icon: Target },
  { title: "Demográfia", url: "/hr/demographics", icon: Users },
  { title: "History & Trends", url: "/hr/trends", icon: Calendar },
  { title: "Export & Jelentések", url: "/hr/export", icon: FileText },
  { title: "Összehasonlítás", url: "/hr/compare", icon: GitCompare },
  { title: "HR Input", url: "/hr/input", icon: SettingsIcon },
  { title: "Audit Indítása", url: "/hr/create-audit", icon: QrCode },
  { title: "Beállítások", url: "/hr/settings", icon: SettingsIcon },
];

export function HRSidebar() {
  const { open } = useSidebar();

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
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>
    </Sidebar>
  );
}
