import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar";
import { HRSidebar } from "@/components/hr/HRSidebar";
import Index from "./pages/Index";
import Auth from "./pages/Auth";
import VerifyEmail from "./pages/VerifyEmail";
import EmailConfirmed from "./pages/EmailConfirmed";
import Magazin from "./pages/Magazin";
import MagazinArticle from "./pages/MagazinArticle";
import Bemutatkozas from "./pages/Bemutatkozas";
import Arak from "./pages/Arak";
import AdminDashboard from "./pages/AdminDashboard";
import SuperAdmin from "./pages/SuperAdmin";
import ApproveAdmin from "./pages/ApproveAdmin";
import HRDashboard from "./pages/HRDashboard";
import EAPAudit from "./pages/hr/EAPAudit";
import Reports from "./pages/hr/Reports";
import Export from "./pages/hr/Export";
import CustomSurvey from "./pages/hr/CustomSurvey";
import Settings from "./pages/hr/Settings";
import UserDashboard from "./pages/UserDashboard";
import Unauthorized from "./pages/Unauthorized";
import NotFound from "./pages/NotFound";
import UnsubscribeSuccess from "./pages/UnsubscribeSuccess";
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
          <Route path="/verify-email" element={<VerifyEmail />} />
          <Route path="/email-confirmed" element={<EmailConfirmed />} />
          <Route path="/magazin" element={<Magazin />} />
          <Route path="/magazin/:slug" element={<MagazinArticle />} />
          <Route path="/bemutatkozas" element={<Bemutatkozas />} />
          <Route path="/arak" element={<Arak />} />
          <Route path="/superadmin" element={<SuperAdmin />} />
          <Route path="/approve-admin" element={<ApproveAdmin />} />
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
            path="/hr/eap-audit" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <EAPAudit />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/reports"
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Reports />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/export" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Export />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/custom-survey" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <CustomSurvey />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/settings" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Settings />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route path="/survey/:token" element={<UserDashboard />} />
          <Route path="/unauthorized" element={<Unauthorized />} />
          <Route path="/unsubscribe-success" element={<UnsubscribeSuccess />} />
          {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
