//
//  ViewController.swift
//  Doação NFP
//
//  Created by Joao Pedro on 5/9/16.
//  Copyright © 2016 Joao Pedro. All rights reserved.
//

import UIKit

class CodeViewController: UIViewController {
    
    @IBOutlet weak var codeTextField: UITextField!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        codeTextField.text = ""
        // Do any additional setup after loading the view, typically from a nib.
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    @IBAction func nextClick(sender: AnyObject) {
        if let qr = codeTextField.text{
            let storyboard = UIStoryboard(name: "Main", bundle: nil)
            let vc = storyboard.instantiateViewControllerWithIdentifier("ONGsTable") as! ONGsViewController
            vc.setupForModalView(withQR: qr)
            self.navigationController?.pushViewController(vc, animated: true)
    
        }
    }
    
    @IBAction func touchBG(sender: AnyObject) {
        view.endEditing(true)
    }
}

