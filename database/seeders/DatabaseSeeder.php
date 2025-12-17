public function run()
{
$this->call([
RoleSeeder::class,
UserSeeder::class,
ProductSeeder::class,
]);
}
=======
public function run()
{
$this->call([
RoleSeeder::class,
UserSeeder::class,
ProductSeeder::class,
TestDataSeeder::class, // Add test data for API testing
]);
}