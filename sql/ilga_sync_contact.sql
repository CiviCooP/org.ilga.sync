/*
 * @author Klaas Eikelboom  <klaas.eikelboom@civicoop.org>
 * @date 25-Feb-2020
 * @license  AGPL-3.0
 */
create table ilga_sync_contact
(
    id              int unsigned auto_increment comment 'Autoincremented type id'
        primary key,
    contact_id      int          null,
    display_name    varchar(128) null,
    ilga_identifier int          null,
    status          varchar(10)  not null
);

create index ilga_sync_contact_indx01
    on ilga_sync_contact (contact_id);

create index ilga_sync_contact_indx02
    on ilga_sync_contact (ilga_identifier);
