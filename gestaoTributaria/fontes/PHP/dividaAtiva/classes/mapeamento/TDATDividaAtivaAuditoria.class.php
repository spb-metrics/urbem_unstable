<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATDividaAtivaAuditoria extends Persistente
{
    
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaAtivaAuditoria()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_ativa_auditoria');

        $this->setCampoCod('cod_grupo');
        $this->setComplementoChave('exercicio, cod_modalidade, total_inscritos');

        $this->AddCampo('exercicio'      , 'varchar', true, '4',true, false );
        $this->AddCampo('cod_grupo'      , 'varchar', true, '' ,true, false );
        $this->AddCampo('cod_modalidade' , 'varchar', true, '' ,true, false );
        $this->AddCampo('total_inscritos', 'varchar', true, '' ,true, false );
        
    }

}
?>