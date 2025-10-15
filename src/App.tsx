import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
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
import UserDashboard from "./pages/UserDashboard";
import Unauthorized from "./pages/Unauthorized";
import NotFound from "./pages/NotFound";
import UnsubscribeSuccess from "./pages/UnsubscribeSuccess";
import ProtectedRoute from "./components/ProtectedRoute";
import MetricsTest from "./pages/hr/MetricsTest";

const queryClient = new QueryClient();

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
            path="/dashboard" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <Index />
              </ProtectedRoute>
            } 
          />
          <Route path="/survey/:token" element={<UserDashboard />} />
          <Route 
            path="/hr/metrics-test" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <MetricsTest />
              </ProtectedRoute>
            } 
          />
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
