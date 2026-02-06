import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Monitor, LogIn, BookOpen, Video, Mic, FileQuestion, Smile, Heart, TrendingUp } from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, PieChart, Pie, Cell, LineChart, Line } from "recharts";

// EAP Online Platform mock data interface
interface EapOnlineData {
  // Login statistics
  logins: {
    current: number;
    cumulated: number;
    trend: { month: string; count: number }[];
  };
  // Self-help content usage
  selfHelp: {
    total: number;
    byCategory: Record<string, number>;
  };
  // Content category statistics (articles, videos, podcasts)
  contentCategories: {
    articles: { views: number; percentage: number };
    videos: { views: number; percentage: number };
    podcasts: { views: number; percentage: number };
  };
  // Assessment statistics
  assessments: {
    completed: number;
    moodMeter: number;
    wellBeing: number;
  };
  // Top categories accessed
  topCategories: { name: string; count: number }[];
  // Usage by device
  deviceUsage: {
    desktop: number;
    mobile: number;
    tablet: number;
  };
}

// Mock data for EAP Online Platform
const MOCK_EAP_ONLINE_DATA: Record<string, EapOnlineData> = {
  hu: {
    logins: {
      current: 234,
      cumulated: 1240,
      trend: [
        { month: 'Jan', count: 180 },
        { month: 'Feb', count: 210 },
        { month: 'Már', count: 195 },
        { month: 'Ápr', count: 230 },
        { month: 'Máj', count: 245 },
        { month: 'Jún', count: 220 },
        { month: 'Júl', count: 190 },
        { month: 'Aug', count: 205 },
        { month: 'Szep', count: 240 },
        { month: 'Okt', count: 260 },
        { month: 'Nov', count: 234 },
        { month: 'Dec', count: 0 },
      ],
    },
    selfHelp: {
      total: 856,
      byCategory: {
        'Stresszkezelés': 245,
        'Mentális egészség': 198,
        'Munka-magánélet egyensúly': 156,
        'Pénzügyi tanácsok': 124,
        'Családi kapcsolatok': 89,
        'Önfejlesztés': 44,
      },
    },
    contentCategories: {
      articles: { views: 1245, percentage: 52 },
      videos: { views: 678, percentage: 28 },
      podcasts: { views: 482, percentage: 20 },
    },
    assessments: {
      completed: 156,
      moodMeter: 423,
      wellBeing: 89,
    },
    topCategories: [
      { name: 'Stresszkezelés', count: 245 },
      { name: 'Mentális egészség', count: 198 },
      { name: 'Munka-magánélet egyensúly', count: 156 },
      { name: 'Pénzügyi tanácsok', count: 124 },
      { name: 'Családi kapcsolatok', count: 89 },
    ],
    deviceUsage: {
      desktop: 45,
      mobile: 42,
      tablet: 13,
    },
  },
  ro: {
    logins: {
      current: 156,
      cumulated: 820,
      trend: [
        { month: 'Jan', count: 120 },
        { month: 'Feb', count: 140 },
        { month: 'Már', count: 135 },
        { month: 'Ápr', count: 160 },
        { month: 'Máj', count: 175 },
        { month: 'Jún', count: 150 },
        { month: 'Júl', count: 130 },
        { month: 'Aug', count: 145 },
        { month: 'Szep', count: 165 },
        { month: 'Okt', count: 180 },
        { month: 'Nov', count: 156 },
        { month: 'Dec', count: 0 },
      ],
    },
    selfHelp: {
      total: 542,
      byCategory: {
        'Stresszkezelés': 165,
        'Mentális egészség': 132,
        'Munka-magánélet egyensúly': 98,
        'Pénzügyi tanácsok': 78,
        'Családi kapcsolatok': 45,
        'Önfejlesztés': 24,
      },
    },
    contentCategories: {
      articles: { views: 856, percentage: 55 },
      videos: { views: 412, percentage: 26 },
      podcasts: { views: 298, percentage: 19 },
    },
    assessments: {
      completed: 98,
      moodMeter: 287,
      wellBeing: 56,
    },
    topCategories: [
      { name: 'Stresszkezelés', count: 165 },
      { name: 'Mentális egészség', count: 132 },
      { name: 'Munka-magánélet egyensúly', count: 98 },
      { name: 'Pénzügyi tanácsok', count: 78 },
      { name: 'Családi kapcsolatok', count: 45 },
    ],
    deviceUsage: {
      desktop: 48,
      mobile: 38,
      tablet: 14,
    },
  },
  sk: {
    logins: {
      current: 112,
      cumulated: 580,
      trend: [
        { month: 'Jan', count: 85 },
        { month: 'Feb', count: 98 },
        { month: 'Már', count: 92 },
        { month: 'Ápr', count: 110 },
        { month: 'Máj', count: 125 },
        { month: 'Jún', count: 108 },
        { month: 'Júl', count: 95 },
        { month: 'Aug', count: 102 },
        { month: 'Szep', count: 118 },
        { month: 'Okt', count: 128 },
        { month: 'Nov', count: 112 },
        { month: 'Dec', count: 0 },
      ],
    },
    selfHelp: {
      total: 385,
      byCategory: {
        'Stresszkezelés': 118,
        'Mentális egészség': 95,
        'Munka-magánélet egyensúly': 72,
        'Pénzügyi tanácsok': 54,
        'Családi kapcsolatok': 32,
        'Önfejlesztés': 14,
      },
    },
    contentCategories: {
      articles: { views: 612, percentage: 54 },
      videos: { views: 298, percentage: 26 },
      podcasts: { views: 226, percentage: 20 },
    },
    assessments: {
      completed: 72,
      moodMeter: 198,
      wellBeing: 42,
    },
    topCategories: [
      { name: 'Stresszkezelés', count: 118 },
      { name: 'Mentális egészség', count: 95 },
      { name: 'Munka-magánélet egyensúly', count: 72 },
      { name: 'Pénzügyi tanácsok', count: 54 },
      { name: 'Családi kapcsolatok', count: 32 },
    ],
    deviceUsage: {
      desktop: 50,
      mobile: 36,
      tablet: 14,
    },
  },
  cz: {
    logins: {
      current: 178,
      cumulated: 920,
      trend: [
        { month: 'Jan', count: 145 },
        { month: 'Feb', count: 168 },
        { month: 'Már', count: 155 },
        { month: 'Ápr', count: 182 },
        { month: 'Máj', count: 198 },
        { month: 'Jún', count: 170 },
        { month: 'Júl', count: 152 },
        { month: 'Aug', count: 165 },
        { month: 'Szep', count: 188 },
        { month: 'Okt', count: 205 },
        { month: 'Nov', count: 178 },
        { month: 'Dec', count: 0 },
      ],
    },
    selfHelp: {
      total: 612,
      byCategory: {
        'Stresszkezelés': 186,
        'Mentális egészség': 149,
        'Munka-magánélet egyensúly': 112,
        'Pénzügyi tanácsok', count: 89,
        'Családi kapcsolatok': 52,
        'Önfejlesztés': 24,
      },
    },
    contentCategories: {
      articles: { views: 978, percentage: 53 },
      videos: { views: 498, percentage: 27 },
      podcasts: { views: 368, percentage: 20 },
    },
    assessments: {
      completed: 112,
      moodMeter: 334,
      wellBeing: 68,
    },
    topCategories: [
      { name: 'Stresszkezelés', count: 186 },
      { name: 'Mentális egészség', count: 149 },
      { name: 'Munka-magánélet egyensúly', count: 112 },
      { name: 'Pénzügyi tanácsok', count: 89 },
      { name: 'Családi kapcsolatok', count: 52 },
    ],
    deviceUsage: {
      desktop: 47,
      mobile: 40,
      tablet: 13,
    },
  },
};

// Color palette
const CHART_COLORS = {
  primary: '#04565f',
  secondary: '#82f5ae',
  tertiary: '#004144',
  accent: '#ffc107',
  highlight: '#6610f2',
};

const CATEGORY_COLORS = [
  CHART_COLORS.primary,
  CHART_COLORS.secondary,
  CHART_COLORS.tertiary,
  CHART_COLORS.accent,
  CHART_COLORS.highlight,
  '#20c997',
];

interface EapOnlineReportSectionProps {
  countryCode: string;
  isCumulated?: boolean;
}

export function EapOnlineReportSection({ countryCode, isCumulated = false }: EapOnlineReportSectionProps) {
  const data = MOCK_EAP_ONLINE_DATA[countryCode] || MOCK_EAP_ONLINE_DATA['hu'];
  
  // Prepare chart data
  const contentCategoryData = [
    { name: 'Cikkek', value: data.contentCategories.articles.views, percentage: data.contentCategories.articles.percentage },
    { name: 'Videók', value: data.contentCategories.videos.views, percentage: data.contentCategories.videos.percentage },
    { name: 'Podcastok', value: data.contentCategories.podcasts.views, percentage: data.contentCategories.podcasts.percentage },
  ];

  const deviceData = [
    { name: 'Asztali', value: data.deviceUsage.desktop },
    { name: 'Mobil', value: data.deviceUsage.mobile },
    { name: 'Tablet', value: data.deviceUsage.tablet },
  ];

  const selfHelpCategoryData = Object.entries(data.selfHelp.byCategory)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count);

  return (
    <>
      {/* Section Header */}
      <div className="col-span-full">
        <div className="flex items-center gap-3 mb-4">
          <div className="p-2 bg-[#04565f]/10 rounded-lg">
            <Monitor className="h-6 w-6 text-[#04565f]" />
          </div>
          <div>
            <h3 className="text-xl font-bold text-foreground">EAP Online Platform Riport</h3>
            <p className="text-sm text-muted-foreground">
              Az online platform használati statisztikái és tartalomfogyasztás
            </p>
          </div>
        </div>
      </div>

      {/* Key Metrics Cards */}
      <div className="col-span-full grid grid-cols-2 md:grid-cols-4 gap-4">
        {/* Logins */}
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-[#04565f]/10 rounded-lg">
                <LogIn className="h-5 w-5 text-[#04565f]" />
              </div>
              <div>
                <p className="text-2xl font-bold text-[#04565f]">
                  {isCumulated ? data.logins.cumulated : data.logins.current}
                </p>
                <p className="text-xs text-muted-foreground">Platform belépések</p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Self-help usage */}
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-[#82f5ae]/20 rounded-lg">
                <BookOpen className="h-5 w-5 text-[#04565f]" />
              </div>
              <div>
                <p className="text-2xl font-bold text-[#04565f]">{data.selfHelp.total}</p>
                <p className="text-xs text-muted-foreground">Önsegítő tartalom</p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Assessments completed */}
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-[#ffc107]/20 rounded-lg">
                <FileQuestion className="h-5 w-5 text-[#04565f]" />
              </div>
              <div>
                <p className="text-2xl font-bold text-[#04565f]">{data.assessments.completed}</p>
                <p className="text-xs text-muted-foreground">Kitöltött felmérés</p>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Mood meter uses */}
        <Card>
          <CardContent className="pt-6">
            <div className="flex items-center gap-3">
              <div className="p-2 bg-[#6610f2]/10 rounded-lg">
                <Smile className="h-5 w-5 text-[#6610f2]" />
              </div>
              <div>
                <p className="text-2xl font-bold text-[#6610f2]">{data.assessments.moodMeter}</p>
                <p className="text-xs text-muted-foreground">Hangulat-mérő</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Login Trend Chart */}
      <Card className="col-span-full md:col-span-2">
        <CardHeader>
          <CardTitle className="text-lg flex items-center gap-2">
            <TrendingUp className="h-5 w-5 text-muted-foreground" />
            Platform belépések trendje
          </CardTitle>
          <CardDescription>Havi bontás az aktuális évben</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="h-[200px]">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={data.logins.trend}>
                <XAxis 
                  dataKey="month" 
                  tick={{ fill: 'hsl(var(--muted-foreground))', fontSize: 11 }}
                />
                <YAxis 
                  tick={{ fill: 'hsl(var(--muted-foreground))', fontSize: 11 }}
                />
                <Tooltip
                  contentStyle={{
                    backgroundColor: 'hsl(var(--background))',
                    border: '1px solid hsl(var(--border))',
                    borderRadius: '8px',
                  }}
                  formatter={(value: number) => [value, 'Belépések']}
                />
                <Line
                  type="monotone"
                  dataKey="count"
                  stroke={CHART_COLORS.primary}
                  strokeWidth={2}
                  dot={{ fill: CHART_COLORS.primary, r: 4 }}
                  activeDot={{ r: 6, fill: CHART_COLORS.secondary }}
                />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </CardContent>
      </Card>

      {/* Content Categories Pie Chart */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Tartalom típusok</CardTitle>
          <CardDescription>Cikkek, videók és podcastok megtekintése</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="h-[180px]">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={contentCategoryData}
                  cx="50%"
                  cy="50%"
                  innerRadius={40}
                  outerRadius={70}
                  paddingAngle={3}
                  dataKey="value"
                >
                  {contentCategoryData.map((_, index) => (
                    <Cell key={`cell-${index}`} fill={CATEGORY_COLORS[index % CATEGORY_COLORS.length]} />
                  ))}
                </Pie>
                <Tooltip
                  contentStyle={{
                    backgroundColor: 'hsl(var(--background))',
                    border: '1px solid hsl(var(--border))',
                    borderRadius: '8px',
                  }}
                  formatter={(value: number, name: string) => [`${value} megtekintés`, name]}
                />
              </PieChart>
            </ResponsiveContainer>
          </div>
          <div className="flex justify-center gap-4 mt-2">
            {contentCategoryData.map((item, index) => (
              <div key={item.name} className="flex items-center gap-1.5 text-sm">
                <div
                  className="w-2.5 h-2.5 rounded-full"
                  style={{ backgroundColor: CATEGORY_COLORS[index] }}
                />
                <span className="text-muted-foreground">{item.name}</span>
                <span className="font-semibold">{item.percentage}%</span>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Device Usage */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg">Eszköz használat</CardTitle>
          <CardDescription>Milyen eszközről érik el a platformot</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {deviceData.map((device, index) => (
              <div key={device.name}>
                <div className="flex items-center justify-between mb-1">
                  <span className="text-sm font-medium">{device.name}</span>
                  <span className="text-sm font-bold" style={{ color: CATEGORY_COLORS[index] }}>
                    {device.value}%
                  </span>
                </div>
                <div className="h-3 bg-muted rounded-full overflow-hidden">
                  <div
                    className="h-full rounded-full transition-all"
                    style={{
                      width: `${device.value}%`,
                      backgroundColor: CATEGORY_COLORS[index],
                    }}
                  />
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      {/* Top Self-help Categories */}
      <Card className="col-span-full md:col-span-2">
        <CardHeader>
          <CardTitle className="text-lg flex items-center gap-2">
            <BookOpen className="h-5 w-5 text-muted-foreground" />
            Top önsegítő kategóriák
          </CardTitle>
          <CardDescription>Legtöbbet használt önsegítő tartalmak kategória szerint</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="h-[200px]">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={selfHelpCategoryData} layout="vertical">
                <XAxis type="number" hide />
                <YAxis
                  dataKey="name"
                  type="category"
                  width={140}
                  tick={{ fill: 'hsl(var(--muted-foreground))', fontSize: 11 }}
                />
                <Tooltip
                  contentStyle={{
                    backgroundColor: 'hsl(var(--background))',
                    border: '1px solid hsl(var(--border))',
                    borderRadius: '8px',
                  }}
                  formatter={(value: number) => [value, 'Használat']}
                />
                <Bar dataKey="count" fill={CHART_COLORS.primary} radius={[0, 4, 4, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </CardContent>
      </Card>

      {/* Assessment Types */}
      <Card>
        <CardHeader>
          <CardTitle className="text-lg flex items-center gap-2">
            <Heart className="h-5 w-5 text-muted-foreground" />
            Felmérések és tesztek
          </CardTitle>
          <CardDescription>Kitöltött felmérések típus szerint</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="flex items-center justify-between p-3 bg-muted/50 rounded-lg">
              <div className="flex items-center gap-2">
                <FileQuestion className="h-4 w-4 text-[#04565f]" />
                <span className="text-sm">Önértékelő kérdőívek</span>
              </div>
              <span className="text-lg font-bold text-[#04565f]">{data.assessments.completed}</span>
            </div>
            <div className="flex items-center justify-between p-3 bg-muted/50 rounded-lg">
              <div className="flex items-center gap-2">
                <Smile className="h-4 w-4 text-[#6610f2]" />
                <span className="text-sm">Hangulat-mérő</span>
              </div>
              <span className="text-lg font-bold text-[#6610f2]">{data.assessments.moodMeter}</span>
            </div>
            <div className="flex items-center justify-between p-3 bg-muted/50 rounded-lg">
              <div className="flex items-center gap-2">
                <Heart className="h-4 w-4 text-[#82f5ae]" />
                <span className="text-sm">Jólléti kérdőív</span>
              </div>
              <span className="text-lg font-bold text-[#04565f]">{data.assessments.wellBeing}</span>
            </div>
          </div>
        </CardContent>
      </Card>
    </>
  );
}
