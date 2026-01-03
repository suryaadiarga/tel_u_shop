import { Link } from "react-router-dom"
import { Button } from "../components/ui/button"
import { Card, CardContent } from "../components/ui/card"

export function LandingPage() {
  return (
    <div className="min-h-screen">
      <div className="mx-auto flex max-w-6xl flex-col gap-12 px-6 pb-16 pt-12">
        <header className="flex flex-wrap items-center justify-between gap-6">
          <div className="font-display text-2xl font-semibold text-ink">TEL-U Shop</div>
          <div className="flex items-center gap-3">
            <Button variant="ghost" asChild>
              <Link to="/login">Login</Link>
            </Button>
            <Button variant="secondary" asChild>
              <Link to="/register">Register</Link>
            </Button>
          </div>
        </header>

        <section className="grid gap-10 lg:grid-cols-[1.15fr_0.85fr]">
          <div className="space-y-6">
            <div className="inline-flex items-center gap-2 rounded-full border border-glacier/20 bg-white/70 px-4 py-2 text-xs font-semibold text-glacier">
              Premium commerce suite
            </div>
            <h1 className="font-display text-4xl font-semibold leading-tight text-ink sm:text-5xl">
              Operate TEL-U Shop with a calmer, faster, and smarter workspace.
            </h1>
            <p className="text-lg text-slate-600">
              Manage catalogs, orders, loyalty, and operations in one unified dashboard tailored for
              customer, merchant, and admin workflows.
            </p>
            <div className="flex flex-wrap gap-3">
              <Button variant="primary" asChild>
                <Link to="/login">Enter workspace</Link>
              </Button>
              <Button variant="outline" asChild>
                <Link to="/register">Create account</Link>
              </Button>
            </div>
          </div>
          <Card className="border border-white/60 bg-white/80">
            <CardContent className="space-y-4">
              <div className="text-sm font-semibold text-slate-500">Live operations glance</div>
              <div className="space-y-3">
                {[
                  "Order fulfillment pipeline",
                  "Wallet + loyalty insights",
                  "Merchant performance signals",
                  "Approval and moderation center",
                ].map((item) => (
                  <div key={item} className="rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-sm">
                    {item}
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </section>
      </div>
    </div>
  )
}
