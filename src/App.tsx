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
import AdminDashboard from "./pages/AdminDashboard";
import HRDashboard from "./pages/HRDashboard";
import CreateAudit from "./pages/hr/CreateAudit";
import UserCategories from "./pages/hr/UserCategories";
import Awareness from "./pages/hr/Awareness";
import TrustWillingness from "./pages/hr/TrustWillingness";
import Usage from "./pages/hr/Usage";
import Impact from "./pages/hr/Impact";
import Motivation from "./pages/hr/Motivation";
import Demographics from "./pages/hr/Demographics";
import Trends from "./pages/hr/Trends";
import Export from "./pages/hr/Export";
import Compare from "./pages/hr/Compare";
import HRInput from "./pages/hr/HRInput";
import Settings from "./pages/hr/Settings";
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
          <Route path="/verify-email" element={<VerifyEmail />} />
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
                  <CreateAudit />
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
          <Route 
            path="/hr/trust" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <TrustWillingness />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/usage" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Usage />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/impact" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Impact />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/motivation" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Motivation />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/demographics" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Demographics />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/trends" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Trends />
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
            path="/hr/compare" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <Compare />
                </HRLayout>
              </ProtectedRoute>
            } 
          />
          <Route 
            path="/hr/input" 
            element={
              <ProtectedRoute allowedRoles={['hr']}>
                <HRLayout>
                  <HRInput />
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
          {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
