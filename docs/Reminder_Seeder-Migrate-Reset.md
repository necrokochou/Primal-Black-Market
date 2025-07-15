### TAKE NOTE

Run this in terminal whenever you use Seeder, Migrate, and Reset (With Approval of Head Admin (LJ))
```

For Users Database

docker exec primal-black-market-service php utils/dbSeederUsersPostgresql.util.php
docker exec primal-black-market-service php utils/dbMigrateUsersPostgresql.util.php

```