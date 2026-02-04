import { ResponsiveContainer, RadialBarChart, RadialBar, PolarAngleAxis } from 'recharts';

interface GaugeChartProps {
  value: number;
  maxValue: number;
  minValue?: number;
  size?: number;
  label?: string;
  sublabel?: string;
  cornerRadius?: number;
  showScale?: boolean;
  gaugeColor?: string;
}

export const GaugeChart = ({ 
  value, 
  maxValue,
  minValue = 0,
  size = 200,
  label,
  sublabel,
  cornerRadius = 30,
  showScale = true,
  gaugeColor
}: GaugeChartProps) => {
  const percentage = ((value - minValue) / (maxValue - minValue)) * 100;
  
  const data = [{ value: percentage }];

  return (
    <div className="flex flex-col items-center" style={{ width: '100%', height: size }}>
      <ResponsiveContainer width="100%" height="100%">
        <RadialBarChart 
          cx="50%"
          cy="50%"
          innerRadius="70%" 
          outerRadius="100%" 
          data={data}
          startAngle={180}
          endAngle={0}
        >
          <PolarAngleAxis type="number" domain={[0, 100]} tick={false} />
          <RadialBar
            background={{ fill: 'hsl(var(--muted))' }}
            dataKey="value"
            cornerRadius={cornerRadius}
            fill={gaugeColor || '#ffc107'}
            isAnimationActive={false}
          />
          <text 
            x="50%" 
            y="58%" 
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
              y="75%" 
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
