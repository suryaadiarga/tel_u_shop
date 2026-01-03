import { Card, CardContent } from "../components/ui/card"
import { Badge } from "../components/ui/badge"

export function DashboardPage() {
  return (
    <div className="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
      <Card>
        <CardContent className="space-y-4">
          <div className="flex items-center justify-between">
            <div>
              <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Workspace</div>
              <div className="font-display text-2xl font-semibold text-ink">Operational pulse</div>
            </div>
            <Badge variant="soft">Live</Badge>
          </div>
          <p className="text-sm text-slate-600">
            Connect to backend endpoints in Batch 2 to populate this panel with live KPI cards and
            activity streams.
          </p>
          <div className="grid gap-3 sm:grid-cols-2">
            {[
              "Realtime order flow",
              "Wallet movements",
              "Merchant performance",
              "Customer retention",
            ].map((item) => (
              <div key={item} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                {item}
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
      <Card>
        <CardContent className="space-y-4">
          <div className="text-xs uppercase tracking-[0.2em] text-slate-400">Today</div>
          <div className="space-y-3">
            {[
              "Sync profile data with /me",
              "Review pending approvals",
              "Monitor checkout success",
            ].map((item) => (
              <div key={item} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600">
                {item}
              </div>
            ))}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
