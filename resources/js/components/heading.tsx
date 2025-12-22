export default function Heading({
    title,
    description,
}: {
    title: string;
    description?: string;
}) {
    return (
        <div className="mb-8 space-y-1">
            <h2 className="text-2xl font-semibold tracking-tight md:text-3xl">
                {title}
            </h2>
            {description && (
                <p className="text-base text-muted-foreground">
                    {description}
                </p>
            )}
        </div>
    );
}
