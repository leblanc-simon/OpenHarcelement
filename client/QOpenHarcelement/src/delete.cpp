/*
Copyright (c) 2012, Simon Leblanc
All rights reserved.

Redistribution and use in source and binary forms, with or without modification
, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
    this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice
    , this list of conditions and the following disclaimer in the documentation
     and/or other materials provided with the distribution.
    * Neither the name of the Simon Leblanc nor the names of its contributors
    may be used to endorse or promote products derived from this software
    without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

#include "delete.h"
#include "ui_delete.h"

#include "mainwindow.h"
#include "harcelement.h"

Delete::Delete(MainWindow *parent) :
    QDialog(parent),
    ui(new Ui::Delete)
{
    ui->setupUi(this);

    QObject::connect(this->ui->buttonBox, SIGNAL(accepted()), this, SLOT(launch()));
    QObject::connect(this->ui->buttonBox, SIGNAL(rejected()), this, SLOT(close()));
}

Delete::~Delete()
{
    delete ui;
}

void Delete::launch()
{
    if (this->ui->lineEditDelete->text().isEmpty()) {
        this->ui->lineEditDelete->setStyleSheet(QString("background-color: rgba(255, 0, 0, 40);"));
        return;
    } else {
        this->ui->lineEditDelete->setStyleSheet(QString(""));
    }

    Harcelement *h = new Harcelement((MainWindow*)this->parentWidget());
    h->hash = this->ui->lineEditDelete->text();
    h->callDelete();

    this->close();
}
