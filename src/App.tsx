import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import { HRSidebar } from "@/components/hr/HRSidebar";
import Index from "./pages/Index";
import Auth from "./pages/Auth";
import AdminDashboard from "./pages/AdminDashboard";
import HRDashboard from "./pages/HRDashboard";
import CreateAudit from "./pages/hr/CreateAudit";
import UserCategories from "./pages/hr/UserCategories";
import Awareness from "./pages/hr/Awareness";
import UserDashboard from "./pages/UserDashboard";
import Unauthorized from "./pages/Unauthorized";
import NotFound from "./pages/NotFound";
import ProtectedRoute from "./components/ProtectedRoute";

const queryClient = new QueryClient();

const HRLayout = ({ children }: { children: React.ReactNode }) => (
  <SidebarProvider>
    <div className="min-h-screen flex w-full">
      <HRSidebar />
      <div className="flex-1 flex flex-col">
        <header className="h-14 border-b flex items-center px-4">
          <SidebarTrigger />
        </header>
        <main className="flex-1">
          {children}
        </main>
      </div>
    </div>
  </SidebarProvider>
);

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Index />} />
          <Route path="/auth" element={<Auth />} />
          <Route 
            path="/admin" 
            element={
              <ProtectedRoute allowedRoles={['admin']}>
                <AdminDashboard />
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <HRDashboard />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/create-audit" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <CreateAudit />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/categories" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <UserCategories />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/awareness" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Awareness />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route path="/survey/:token" element={<UserDashboard />} />
          <Route path="/unauthorized" element={<Unauthorized />} />
          {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
