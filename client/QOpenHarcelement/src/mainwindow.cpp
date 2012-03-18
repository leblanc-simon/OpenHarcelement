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

#include "mainwindow.h"
#include "ui_mainwindow.h"

#include "harcelement.h"
#include "delete.h"
#include "about.h"

#include <QtNetwork/QHttp>
#include <QtNetwork/QHttpResponseHeader>
#include <QMessageBox>

MainWindow::MainWindow(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::MainWindow)
{
    ui->setupUi(this);

    this->initTime();

    // Les boutons d'action
    QWidget::connect(this->ui->buttonBox, SIGNAL(accepted()), this, SLOT(launch()));
    QWidget::connect(this->ui->buttonBox, SIGNAL(rejected()), this, SLOT(close()));

    // Le menu
    QWidget::connect(this->ui->actionClose, SIGNAL(triggered()), this, SLOT(close()));
    QWidget::connect(this->ui->actionDelete, SIGNAL(triggered()), this, SLOT(launchDelete()));
    QWidget::connect(this->ui->actionAbout, SIGNAL(triggered()), this, SLOT(showAbout()));

}

MainWindow::~MainWindow()
{
    delete ui;
}

void MainWindow::launch()
{
    // Vérification des champs
    if (this->checkField() == false) {
        return;
    }

    // Construction de la requête
    Harcelement *h = new Harcelement(this);

    h->name = this->ui->lineEditName->text();
    h->email = this->ui->lineEditEmail->text();
    h->email_victim = this->ui->lineEditEmailVictim->text();
    h->subject = this->ui->lineEditSubject->text();
    h->message = this->ui->textEditMessage->toPlainText();
    h->time = this->time.at(this->ui->comboBox->currentIndex());
    h->callAdd();
}

void MainWindow::launchDelete()
{
    Delete* delete_window = new Delete(this);
    delete_window->exec();
}

void MainWindow::initTime()
{
    // Les différents horaires
    this->time.append("PT1M");
    this->time.append("PT10M");
    this->time.append("PT6H");
    this->time.append("P1D");

    this->time_str.append("Toutes les minutes");
    this->time_str.append("Toutes les 10 minutes");
    this->time_str.append("Toutes les 6 heures");
    this->time_str.append("Tous les jours");

    this->ui->comboBox->addItem(QString(""), -1);
    for (int i = 0; i < this->time.count(); i++) {
        this->ui->comboBox->addItem(this->time_str.at(i), i);
    }
}

bool MainWindow::checkField()
{
    bool error = false;

    if (this->ui->lineEditEmail->text().isEmpty()) {
        error = true;
        this->ui->lineEditEmail->setStyleSheet(QString("background-color: rgba(255, 0, 0, 40);"));
    } else {
        this->ui->lineEditEmail->setStyleSheet(QString(""));
    }

    if (this->ui->lineEditEmailVictim->text().isEmpty()) {
        error = true;
        this->ui->lineEditEmailVictim->setStyleSheet(QString("background-color: rgba(255, 0, 0, 40);"));
    } else {
        this->ui->lineEditEmailVictim->setStyleSheet(QString(""));
    }

    if (this->ui->lineEditName->text().isEmpty()) {
        error = true;
        this->ui->lineEditName->setStyleSheet(QString("background-color: rgba(255, 0, 0, 40);"));
    } else {
        this->ui->lineEditName->setStyleSheet(QString(""));
    }

    if (this->ui->lineEditSubject->text().isEmpty()) {
        error = true;
        this->ui->lineEditSubject->setStyleSheet(QString("background-color: rgba(255, 0, 0, 40);"));
    } else {
        this->ui->lineEditSubject->setStyleSheet(QString(""));
    }

    if (this->ui->textEditMessage->toPlainText().isEmpty()) {
        error = true;
        this->ui->textEditMessage->setStyleSheet(QString("background-color: rgba(255, 0, 0, 40);"));
    } else {
        this->ui->textEditMessage->setStyleSheet(QString(""));
    }

    if (this->ui->comboBox->itemData(this->ui->comboBox->currentIndex()).toInt() == -1) {
        error = true;
        this->ui->comboBox->setStyleSheet(QString("background-color: rgba(255, 0, 0, 40);"));
    } else {
        this->ui->comboBox->setStyleSheet(QString(""));
    }

    return !error;
}


void MainWindow::readHeader(const QHttpResponseHeader& header)
{
    QMessageBox box;

    if (header.statusCode() == 200 || header.statusCode() == 201) {
        box.setText(tr("Votre demande concernant le harcèlement a été prise en compte"));
        box.setIcon(QMessageBox::Information);
    } else {
        box.setText(tr("Votre demande concernant le harcèlement n'a pas pu être prise en compte"));
        box.setIcon(QMessageBox::Critical);
    }

    box.setStandardButtons(QMessageBox::Ok);
    box.setDefaultButton(QMessageBox::Ok);
    box.setWindowTitle(tr("Résultat de votre demande"));
    box.exec();
}


/**
 * show the about dialog [slot]
 */
void MainWindow::showAbout()
{
    About* w = new About(this);
    w->show();
}

