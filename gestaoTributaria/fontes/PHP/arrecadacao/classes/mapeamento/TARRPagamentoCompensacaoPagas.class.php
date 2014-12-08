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
/**
    * Classe de mapeamento da tabela ARRECADACAO.PAGAMENTO_COMPENSACAO_PAGAS
    * Data de Criação: 12/12/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPagamentoCompensacaoPagas.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.10
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRPagamentoCompensacaoPagas extends Persistente
{
    public function TARRPagamentoCompensacaoPagas()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.pagamento_compensacao_pagas');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_compensacao,numeracao,ocorrencia_pagamento,cod_convenio');

        $this->AddCampo( 'cod_compensacao', 'integer', true, '', true, true);
        $this->AddCampo( 'numeracao', 'varchar', true, '17', true, true);
        $this->AddCampo( 'ocorrencia_pagamento', 'integer', true, '', true, true);
        $this->AddCampo( 'cod_convenio', 'integer', true, '', true, true);
    }
}// end of class
?>
