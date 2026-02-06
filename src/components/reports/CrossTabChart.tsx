import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, Legend } from "recharts";

interface CrossTabChartProps {
  title: string;
  description?: string;
  data: Record<string, Record<string, number>>;
  labelMap?: Record<string, string>;
  categoryLabelMap?: Record<string, string>;
  colors?: string[];
}

const DEFAULT_COLORS = [
  '#04565f',
  '#82f5ae',
  '#004144',
  '#ffc107',
  '#6610f2',
  '#20c997',
];

export function CrossTabChart({ 
  title, 
  description, 
  data,
  labelMap = {},
  categoryLabelMap = {},
  colors = DEFAULT_COLORS,
}: CrossTabChartProps) {
  // Transform data for stacked bar chart
  // data is: { problemType: { gender/age: count } }
  const problemTypes = Object.keys(data);
  
  if (problemTypes.length === 0) {
    return (
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">{title}</CardTitle>
          {description && <CardDescription>{description}</CardDescription>}
        </CardHeader>
        <CardContent>
          <div className="flex items-center justify-center h-[200px] text-muted-foreground">
            Nincs adat
          </div>
        </CardContent>
      </Card>
    );
  }

  // Get all unique categories (gender values or age values)
  const allCategories = new Set<string>();
  for (const pt of problemTypes) {
    for (const cat of Object.keys(data[pt])) {
      allCategories.add(cat);
    }
  }
  const categories = Array.from(allCategories);

  // Build chart data: each item is a problem type with counts for each category
  const chartData = problemTypes.map(pt => {
    const row: Record<string, string | number> = {
      name: labelMap[pt] || pt,
    };
    let total = 0;
    for (const cat of categories) {
      const count = data[pt][cat] || 0;
      row[cat] = count;
      total += count;
    }
    row.total = total;
    return row;
  }).sort((a, b) => (b.total as number) - (a.total as number));

  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-lg">{title}</CardTitle>
        {description && <CardDescription>{description}</CardDescription>}
      </CardHeader>
      <CardContent>
        <div className="h-[300px]">
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={chartData} layout="vertical">
              <XAxis type="number" hide />
              <YAxis 
                dataKey="name" 
                type="category" 
                width={100}
                tick={{ fill: 'hsl(var(--muted-foreground))', fontSize: 11 }}
              />
              <Tooltip 
                contentStyle={{ 
                  backgroundColor: 'hsl(var(--background))',
                  border: '1px solid hsl(var(--border))',
                  borderRadius: '8px'
                }}
              />
              <Legend 
                formatter={(value) => categoryLabelMap[value] || value}
              />
              {categories.map((cat, index) => (
                <Bar 
                  key={cat}
                  dataKey={cat}
                  name={categoryLabelMap[cat] || cat}
                  stackId="stack"
                  fill={colors[index % colors.length]}
                />
              ))}
            </BarChart>
          </ResponsiveContainer>
        </div>
      </CardContent>
    </Card>
  );
}
