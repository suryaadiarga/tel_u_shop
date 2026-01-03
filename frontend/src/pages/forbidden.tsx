import { Link } from "react-router-dom"
import { Card, CardContent } from "../components/ui/card"
import { Button } from "../components/ui/button"

export function ForbiddenPage() {
  return (
    <div className="mx-auto max-w-xl">
      <Card>
        <CardContent className="space-y-3">
          <div className="font-display text-xl font-semibold text-ink">Access denied</div>
          <p className="text-sm text-slate-600">
            You do not have permission to view this area. If you believe this is a mistake, contact an administrator.
          </p>
          <Button asChild>
            <Link to="/app">Back to workspace</Link>
          </Button>
        </CardContent>
      </Card>
    </div>
  )
}
