drop table if exists mailer cascade;
create table mailer (
    id int unsigned not null primary key auto_increment,
    queue_name char(16) not null,
    mail text not null,
    created timestamp default now() not null,    
    status enum('waiting','processing','sent') not null
);
