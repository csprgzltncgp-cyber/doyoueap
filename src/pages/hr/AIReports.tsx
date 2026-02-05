 import { useState, useRef, useEffect } from 'react';
 import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
 import { Button } from '@/components/ui/button';
 import { Input } from '@/components/ui/input';
 import { ScrollArea } from '@/components/ui/scroll-area';
 import { Sparkles, Send, Bot, User, Loader2 } from 'lucide-react';
 import { supabase } from '@/integrations/supabase/client';
 import { toast } from 'sonner';
 import { 
   PieChart, Pie, Cell, BarChart, Bar, LineChart, Line, 
   XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend 
 } from 'recharts';
 import { Progress } from '@/components/ui/progress';
 
 interface ChatMessage {
   role: 'user' | 'assistant';
   content: string;
   charts?: ChartData[];
 }
 
 interface ChartData {
   type: 'bar' | 'pie' | 'line' | 'progress' | 'gauge';
   title: string;
   data: Array<{ name?: string; value: number; max?: number; color?: string }>;
 }
 
 const EXAMPLE_PROMPTS = [
   "Mutasd meg a probléma típusok megoszlását kördiagramon",
   "Készíts havi használati statisztikát oszlopdiagrammal",
   "Mutasd a program használati arányt és az elégedettségi indexet",
   "Készíts átfogó negyedéves riportot grafikonokkal",
   "Hasonlítsd össze a konzultáció típusokat (személyes, telefon, online)",
 ];
 
 const GaugeChart = ({ value, max = 100, title }: { value: number; max?: number; title: string }) => {
   const percentage = (value / max) * 100;
   const rotation = (percentage / 100) * 180 - 90;
   
   return (
     <div className="flex flex-col items-center">
       <h4 className="text-sm font-medium mb-2">{title}</h4>
       <div className="relative w-32 h-16 overflow-hidden">
         <div className="absolute w-32 h-32 rounded-full border-8 border-muted" 
              style={{ clipPath: 'polygon(0 0, 100% 0, 100% 50%, 0 50%)' }} />
         <div 
           className="absolute w-32 h-32 rounded-full border-8 border-[#04565f]" 
           style={{ 
             clipPath: 'polygon(0 0, 100% 0, 100% 50%, 0 50%)',
             transform: `rotate(${rotation - 90}deg)`,
             transformOrigin: 'center center'
           }} 
         />
         <div className="absolute bottom-0 left-1/2 -translate-x-1/2 text-lg font-bold text-[#04565f]">
           {value}%
         </div>
       </div>
     </div>
   );
 };
 
 const ChartRenderer = ({ chart }: { chart: ChartData }) => {
   switch (chart.type) {
     case 'pie':
       return (
         <Card className="p-4">
           <h4 className="text-sm font-medium mb-4 text-center">{chart.title}</h4>
           <ResponsiveContainer width="100%" height={200}>
             <PieChart>
               <Pie
                 data={chart.data}
                 cx="50%"
                 cy="50%"
                 innerRadius={40}
                 outerRadius={70}
                 paddingAngle={2}
                 dataKey="value"
                 label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                 labelLine={false}
               >
                {chart.data.map((entry, index) => {
                    // Pie chart color rule: dark green, green, light green, then faded greens
                    const pieColors = ['#04565f', '#82f5ae', '#004144'];
                    const getFadedGreen = (i: number) => `rgba(130, 245, 174, ${0.7 - (i - 3) * 0.15})`;
                    const defaultColor = index < 3 ? pieColors[index] : getFadedGreen(index);
                    return <Cell key={`cell-${index}`} fill={entry.color || defaultColor} />;
                  })}
               </Pie>
               <Tooltip />
             </PieChart>
           </ResponsiveContainer>
         </Card>
       );
       
     case 'bar':
       return (
         <Card className="p-4">
           <h4 className="text-sm font-medium mb-4 text-center">{chart.title}</h4>
           <ResponsiveContainer width="100%" height={200}>
             <BarChart data={chart.data}>
               <CartesianGrid strokeDasharray="3 3" />
               <XAxis dataKey="name" fontSize={12} />
               <YAxis fontSize={12} />
               <Tooltip />
               <Bar dataKey="value" fill="#04565f" radius={[4, 4, 0, 0]} />
             </BarChart>
           </ResponsiveContainer>
         </Card>
       );
       
     case 'line':
       return (
         <Card className="p-4">
           <h4 className="text-sm font-medium mb-4 text-center">{chart.title}</h4>
           <ResponsiveContainer width="100%" height={200}>
             <LineChart data={chart.data}>
               <CartesianGrid strokeDasharray="3 3" />
               <XAxis dataKey="name" fontSize={12} />
               <YAxis fontSize={12} />
               <Tooltip />
               <Line type="monotone" dataKey="value" stroke="#04565f" strokeWidth={2} dot={{ fill: '#04565f' }} />
             </LineChart>
           </ResponsiveContainer>
         </Card>
       );
       
     case 'progress':
       return (
         <Card className="p-4">
           <h4 className="text-sm font-medium mb-3">{chart.title}</h4>
           {chart.data.map((item, index) => (
             <div key={index} className="space-y-2">
               <div className="flex justify-between text-sm">
                 <span>{item.name}</span>
                 <span className="font-medium">{item.value}%</span>
               </div>
               <Progress value={item.value} className="h-3" />
             </div>
           ))}
         </Card>
       );
       
     case 'gauge':
       return (
         <Card className="p-4 flex justify-center">
           <GaugeChart 
             value={chart.data[0]?.value || 0} 
             max={chart.data[0]?.max || 100} 
             title={chart.title} 
           />
         </Card>
       );
       
     default:
       return null;
   }
 };
 
 const AIReports = () => {
   const [messages, setMessages] = useState<ChatMessage[]>([]);
   const [input, setInput] = useState('');
   const [isLoading, setIsLoading] = useState(false);
   const scrollRef = useRef<HTMLDivElement>(null);
 
   useEffect(() => {
     if (scrollRef.current) {
       scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
     }
   }, [messages]);
 
   const sendMessage = async (messageText?: string) => {
     const text = messageText || input.trim();
     if (!text || isLoading) return;
 
     const userMessage: ChatMessage = { role: 'user', content: text };
     setMessages(prev => [...prev, userMessage]);
     setInput('');
     setIsLoading(true);
 
     try {
       const { data, error } = await supabase.functions.invoke('ai-reports', {
         body: { 
           messages: [...messages, userMessage].map(m => ({ role: m.role, content: m.content }))
         }
       });
 
       if (error) {
         throw new Error(error.message);
       }
 
       if (data.error) {
         toast.error(data.error);
         setMessages(prev => [...prev, { 
           role: 'assistant', 
           content: 'Sajnálom, hiba történt a kérés feldolgozása közben. Kérlek próbáld újra.' 
         }]);
       } else {
         setMessages(prev => [...prev, { 
           role: 'assistant', 
           content: data.text || 'Itt van a kért riport:',
           charts: data.charts || []
         }]);
       }
     } catch (error) {
       console.error('AI reports error:', error);
       toast.error('Nem sikerült kapcsolódni az AI szolgáltatáshoz');
       setMessages(prev => [...prev, { 
         role: 'assistant', 
         content: 'Sajnálom, hiba történt. Kérlek próbáld újra később.' 
       }]);
     } finally {
       setIsLoading(false);
     }
   };
 
   const handleKeyPress = (e: React.KeyboardEvent) => {
     if (e.key === 'Enter' && !e.shiftKey) {
       e.preventDefault();
       sendMessage();
     }
   };
 
   return (
     <div className="space-y-6 pt-20 md:pt-0">
       {/* Header */}
       <div className="flex items-center justify-between">
         <div>
           <h1 className="text-2xl font-bold text-foreground flex items-center gap-2">
             <Sparkles className="h-6 w-6 text-[#04565f]" />
             AI Riportok
           </h1>
           <p className="text-muted-foreground mt-1">
             Kérj egyedi riportokat az AI-tól saját szempontjaid alapján
           </p>
         </div>
       </div>
 
       <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
         {/* Chat Panel */}
         <Card className="lg:col-span-2 flex flex-col h-[600px]">
           <CardHeader className="pb-3">
             <CardTitle className="text-lg flex items-center gap-2">
               <Bot className="h-5 w-5" />
               Riport Asszisztens
             </CardTitle>
             <CardDescription>
               Írd le, milyen riportot szeretnél, és az AI létrehozza neked
             </CardDescription>
           </CardHeader>
           <CardContent className="flex-1 flex flex-col overflow-hidden">
             {/* Messages */}
             <ScrollArea className="flex-1 pr-4" ref={scrollRef}>
               <div className="space-y-4">
                 {messages.length === 0 && (
                   <div className="text-center text-muted-foreground py-8">
                     <Bot className="h-12 w-12 mx-auto mb-4 opacity-50" />
                     <p>Üdvözöllek! Milyen riportot készítsek?</p>
                     <p className="text-sm mt-2">Pl.: "Mutasd a probléma típusok megoszlását"</p>
                   </div>
                 )}
                 
                 {messages.map((message, index) => (
                   <div key={index} className={`flex gap-3 ${message.role === 'user' ? 'justify-end' : 'justify-start'}`}>
                     {message.role === 'assistant' && (
                       <div className="w-8 h-8 rounded-full bg-[#04565f] flex items-center justify-center flex-shrink-0">
                         <Bot className="h-4 w-4 text-white" />
                       </div>
                     )}
                     <div className={`max-w-[80%] space-y-3 ${message.role === 'user' ? 'order-first' : ''}`}>
                       <div className={`rounded-lg px-4 py-2 ${
                         message.role === 'user' 
                           ? 'bg-[#04565f] text-white' 
                           : 'bg-muted'
                       }`}>
                         {message.content}
                       </div>
                       
                       {/* Render charts */}
                       {message.charts && message.charts.length > 0 && (
                         <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                           {message.charts.map((chart, chartIndex) => (
                             <ChartRenderer key={chartIndex} chart={chart} />
                           ))}
                         </div>
                       )}
                     </div>
                     {message.role === 'user' && (
                       <div className="w-8 h-8 rounded-full bg-muted flex items-center justify-center flex-shrink-0">
                         <User className="h-4 w-4" />
                       </div>
                     )}
                   </div>
                 ))}
                 
                 {isLoading && (
                   <div className="flex gap-3 justify-start">
                     <div className="w-8 h-8 rounded-full bg-[#04565f] flex items-center justify-center">
                       <Bot className="h-4 w-4 text-white" />
                     </div>
                     <div className="bg-muted rounded-lg px-4 py-2 flex items-center gap-2">
                       <Loader2 className="h-4 w-4 animate-spin" />
                       <span>Riport készítése...</span>
                     </div>
                   </div>
                 )}
               </div>
             </ScrollArea>
 
             {/* Input */}
             <div className="flex gap-2 mt-4 pt-4 border-t">
               <Input
                 value={input}
                 onChange={(e) => setInput(e.target.value)}
                 onKeyPress={handleKeyPress}
                 placeholder="Írd le, milyen riportot szeretnél..."
                 disabled={isLoading}
                 className="flex-1"
               />
               <Button 
                 onClick={() => sendMessage()} 
                 disabled={!input.trim() || isLoading}
                 className="bg-[#04565f] hover:bg-[#04565f]/90"
               >
                 <Send className="h-4 w-4" />
               </Button>
             </div>
           </CardContent>
         </Card>
 
         {/* Example Prompts */}
         <Card className="h-fit">
           <CardHeader>
             <CardTitle className="text-lg">Példa kérések</CardTitle>
             <CardDescription>
               Kattints egy példára az induláshoz
             </CardDescription>
           </CardHeader>
           <CardContent className="space-y-2">
             {EXAMPLE_PROMPTS.map((prompt, index) => (
               <Button
                 key={index}
                 variant="outline"
                  className="w-full justify-start text-left h-auto py-3 px-4 whitespace-normal"
                 onClick={() => sendMessage(prompt)}
                 disabled={isLoading}
               >
                 <Sparkles className="h-4 w-4 mr-2 flex-shrink-0 text-[#04565f]" />
                  <span className="text-sm break-words">{prompt}</span>
               </Button>
             ))}
           </CardContent>
         </Card>
       </div>
     </div>
   );
 };
 
 export default AIReports;