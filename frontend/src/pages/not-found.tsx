import { Link } from "react-router-dom"
import { Button } from "../components/ui/button"
import { Card, CardContent } from "../components/ui/card"

export function NotFoundPage() {
  return (
    <div className="mx-auto flex min-h-screen max-w-5xl items-center justify-center px-6">
      <Card className="w-full max-w-lg">
        <CardContent className="space-y-4 text-center">
          <div className="font-display text-3xl font-semibold text-ink">Page not found</div>
          <p className="text-sm text-slate-600">We could not find that route. Return to the workspace.</p>
          <Button asChild>
            <Link to="/">Back to landing</Link>
          </Button>
        </CardContent>
      </Card>
    </div>
  )
}
