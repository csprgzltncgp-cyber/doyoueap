import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { BookOpen, Calculator, TrendingUp, Users, Shield, Activity, Target, Eye } from 'lucide-react';

const Methodology = () => {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold mb-2">Metodológia és Magyarázatok</h2>
        <p className="text-muted-foreground">A mutatók és számítások részletes leírása</p>
      </div>
      <div className="text-center py-12 text-muted-foreground">
        Még nincs kiértékelt adat
      </div>
    </div>
  );
};

export default Methodology;
