//
//  ONGDetailViewController.swift
//  Doação NFP
//
//  Created by Joao Pedro on 5/10/16.
//  Copyright © 2016 Joao Pedro. All rights reserved.
//

import UIKit

class ONGDetailViewController: UIViewController {
    
    @IBOutlet weak var sobreLabel: UILabel!
    @IBOutlet weak var selecionarBtn: UIButton!
    @IBOutlet weak var ongcommentText: UITextView!
    var qrcode = "";
    var ong:ONG?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.selecionarBtn.hidden = qrcode == ""
        self.ongcommentText.hidden = qrcode == ""
        sobreLabel.text = ong?.Info
        self.title = ong?.Nome
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    @IBAction func selecionar(sender: AnyObject) {
        //send request
        print(qrcode)
        let doacao = Doacao(qr: qrcode, ong: ong!,comment: ongcommentText.text)
        DoacaoSingleton.sharedInstance.historico.insert(doacao, atIndex: 0)
        self.navigationController?.popToRootViewControllerAnimated(true)
    }
    
}

