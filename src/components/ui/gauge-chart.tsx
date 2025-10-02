import { ResponsiveContainer, RadialBarChart, RadialBar } from 'recharts';

interface GaugeChartProps {
  value: number;
  maxValue: number;
  size?: number;
  label?: string;
  sublabel?: string;
}

export const GaugeChart = ({ 
  value, 
  maxValue, 
  size = 200,
  label,
  sublabel
}: GaugeChartProps) => {
  const percentage = (value / maxValue) * 100;
  
  // Determine color based on percentage
  const getColor = () => {
    if (percentage >= 70) return '#22c55e'; // green
    if (percentage >= 40) return '#eab308'; // yellow
    return '#ef4444'; // red
  };

  const data = [
    { 
      name: 'value',
      value: percentage,
      fill: getColor()
    }
  ];

  return (
    <div className="flex flex-col items-center">
      <ResponsiveContainer width="100%" height={size}>
        <RadialBarChart 
          cx="50%"
          cy="50%"
          innerRadius="70%" 
          outerRadius="100%" 
          data={data}
          startAngle={180}
          endAngle={0}
          barSize={20}
        >
          <RadialBar
            background={{ fill: 'hsl(var(--muted))' }}
            dataKey="value"
            cornerRadius={10}
          />
          <text 
            x="50%" 
            y="45%" 
            textAnchor="middle" 
            dominantBaseline="middle" 
            className="fill-foreground"
          >
            <tspan className="text-4xl font-bold">
              {label || value.toFixed(1)}
            </tspan>
          </text>
          {sublabel && (
            <text 
              x="50%" 
              y="65%" 
              textAnchor="middle" 
              dominantBaseline="middle" 
              className="fill-muted-foreground text-sm"
            >
              {sublabel}
            </text>
          )}
        </RadialBarChart>
      </ResponsiveContainer>
    </div>
  );
};
