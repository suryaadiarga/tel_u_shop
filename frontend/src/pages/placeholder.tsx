import { Card, CardContent } from "../components/ui/card"
import { Skeleton } from "../components/ui/skeleton"

export function PlaceholderPage({
  title,
  description,
}: {
  title: string
  description: string
}) {
  return (
    <Card>
      <CardContent className="space-y-4">
        <div className="text-sm font-semibold text-slate-500">{title}</div>
        <p className="text-base text-slate-600">{description}</p>
        <div className="grid gap-3 sm:grid-cols-3">
          <Skeleton className="h-16" />
          <Skeleton className="h-16" />
          <Skeleton className="h-16" />
        </div>
      </CardContent>
    </Card>
  )
}
